<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AwardShortlistStageRequest;
use App\Http\Resources\AwardShortlistStageResource;
use App\Models\Award;
use App\Models\AwardShortlistStage;
use App\Services\AwardService;
use App\Services\ExcelService;
use App\Services\HttpResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AwardShortlistStageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $stages = AwardShortlistStage::query()
            ->withCount('awards');

        // Filter by structural parameters defined in your blueprint
        if ($request->has('active')) {
            $stages->where('active', $request->boolean('active'));
        }

        if ($request->has('mark_as_final')) {
            $stages->where('mark_as_final', $request->boolean('mark_as_final'));
        }

        // Ordered cleanly by processing priority sequence, then newest
        $stages = $stages->orderBy('position', 'asc')->latest()->get();

        return view('admin.shortliststages.index', compact('stages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shortliststages.edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AwardShortlistStageRequest $request)
    {
        $validated = $request->validated();

        // Enforce fallback position calculations or auto-slug generation parameters
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['award_type'] = $request->input('award_type') ?: null;
        $validated['active'] = $request->boolean('active');
        $validated['mark_as_final'] = $request->boolean('mark_as_final');
        $validated['system_conditions'] = $this->buildSystemConditions($request);
        $validated = array_intersect_key($validated, array_flip([
            'title',
            'description',
            'slug',
            'award_type',
            'stage_engine',
            'system_conditions',
            'position',
            'active',
            'mark_as_final',
        ]));

        AwardShortlistStage::create($validated);

        return redirect()
            ->route('shortlist.index')
            ->with('message', 'Shortlist Stage created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AwardShortlistStage $shortlist)
    {
        $awardShortlistStage = $shortlist;

        return view('admin.shortliststages.edit', compact('awardShortlistStage'));
    }

    /**
     * Display the specified resource via API payload endpoints.
     */
    public function show(AwardShortlistStage $shortlist)
    {
        return HttpResponseService::success(
            'Retrieved successfully',
            new AwardShortlistStageResource($shortlist),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AwardShortlistStageRequest $request, AwardShortlistStage $shortlist)
    {
        $validated = $request->validated();

        // Dynamic update management tracking modifications to title string lines
        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['active'] = $request->boolean('active');
        $validated['mark_as_final'] = $request->boolean('mark_as_final');
        $validated['award_type'] = $request->input('award_type') ?: null;
        $validated['system_conditions'] = $this->buildSystemConditions($request);
        $validated = array_intersect_key($validated, array_flip([
            'title',
            'description',
            'slug',
            'award_type',
            'stage_engine',
            'system_conditions',
            'position',
            'active',
            'mark_as_final',
        ]));

        $shortlist->update($validated);

        return redirect()
            ->route('shortlist.index')
            ->with('message', 'Shortlist Stage updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AwardShortlistStage $shortlist)
    {
        // Safety lock verification parameters can be included here depending on system pipeline rules
        $shortlist->delete();

        return back()->with('message', 'Deleted successfully');
    }

    public function moveMatchingAwards(AwardShortlistStage $shortlist, AwardService $awardService)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$shortlist->active) {
            return back()->with('error', 'Only active shortlist stages can move matching awards.');
        }

        if ($shortlist->stage_engine !== 'system') {
            return back()->with('error', 'Only system-powered stages can move awards by criteria.');
        }

        $moved = $awardService->applySystemShortlistStage($shortlist);

        return back()->with(
            'message',
            "{$moved} award entr" . ($moved === 1 ? 'y was' : 'ies were') . " moved into {$shortlist->title}."
        );
    }

    protected function buildSystemConditions(AwardShortlistStageRequest $request): array
    {
        return [
            'chapter_status' => $request->boolean('chapter_status'),
            'zone_status' => $request->boolean('zone_status'),
            'field_status' => $request->boolean('field_status'),
            'approval_match' => $request->input('approval_match', 'all'),
            'approval_count' => (int) $request->input('approval_count', 1),
            'uses_report_metrics' => $request->boolean('uses_report_metrics'),
            'report_metric_months' => $request->filled('report_metric_months')
                ? (int) $request->input('report_metric_months')
                : null,
            'report_statuses' => [
                'zone_status' => $request->boolean('report_zone_status'),
                'field_status' => $request->boolean('report_field_status'),
                'national_status' => $request->boolean('report_national_status'),
            ],
            'report_approval_match' => $request->input('report_approval_match', 'all'),
            'report_approval_count' => (int) $request->input('report_approval_count', 1),
        ];
    }

    public function downloadEntries(AwardShortlistStage $shortlist){
        $awards = Award::with([
                'chapter',
                'zone',
                'field',
                'entry',
            ])
            ->where('current_shortlist_stage_id', $shortlist->id)
            ->orderBy('chapter_id')
            ->get()
            ->sortBy(fn ($award) => $award->chapter?->name
                ?? $award->entry?->select_institution
                ?? 'ZZZ')
            ->values();

        if ($awards->isEmpty()) {
            return back()->with(
                'error',
                'No award records found to download.'
            );
        }

        $fileName = $shortlist->title. '-' .now()->format('Y-m-d-His').'.xlsx';

        $headers = [
            'Nominee Name',
            'Email Address',
            'Phone Number',
            'Chapter',
            'Zone',
            'Field',
            'Gender',
            'Faculty',
            'Department',
            'Course',
            'CGPA',
            'Account Details',
            'Submission Date',
            'National Status',
        ];

        $allRows = [];

        foreach ($awards as $award) {
            $allRows[] = [
                'Nominee Name' => $award->name,
                'Email Address' => $award->email,
                'Phone Number' => $award->phone,

                'Chapter' => $award->chapter?->name
                    ?? $award->entry?->select_institution
                    ?? '—',
                'Zone' => $award->zone?->name ?? '—',
                'Field' => $award->field?->name ?? '—',
                'Gender' => $award->entry->gender ?? '-',
                'Faculty' => $award->entry->faculty_name,
                'Department' => $award->entry->department ?? '-',
                'Course' => $award->entry->course_of_study ?? '-',
                'CGPA' => $award->entry->cgpa ?? '-',
                'Account Details' => $award->account,

                'Submission Date' => $award->created_at?->format('Y-m-d h:i A'),

                'National Status' => match ((int) $award->national_status) {
                    0 => 'Pending',
                    1 => 'Approved',
                    2 => 'Rejected',
                    default => 'Unknown',
                },
            ];
        }

        return ExcelService::download(
            $allRows,
            $headers,
            $fileName
        );

    }
}
