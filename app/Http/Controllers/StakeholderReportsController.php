<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Reports;
use App\Models\Setting;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use App\Models\StakeholderReport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\StakeholderReportAnswer;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;
use App\Services\ReportNotificationService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StakeholderReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::guard('stakeholder')->user();
        $role = $user->role_id;
        
        // Base queries
        $chapters = Chapter::query();
        $zones = Zone::query();
        $fields = Field::query();

        // Scope variables for filtering reports
        $chapterIds = collect();
        $zoneIds = collect();
        $fieldIds = collect();
        
        /** =====================
         * ROLE-BASED SCOPING
         * ===================== */
        if (in_array($role, chapterStakeholders())) {
            $chapterIds = collect([$user->chapter_id]);
            $zoneIds = collect([$user->zone_id]);
            $fieldIds = collect([$user->field_id]);
        } elseif (in_array($role, zoneStakeholders())) {
            $zoneIds = collect([$user->zone_id]);

            // All chapters under this zone
            $chapterIds = Chapter::where('zone_id', $user->zone_id)->pluck('id');
            // Fields that contain this zone
            $fieldIds = Field::whereHas('zones', fn($q) => $q->where('id', $user->zone_id))
                ->pluck('id');
        } elseif (in_array($role, fieldStakeholders())) {
            $fieldIds = collect([$user->field_id]);
            // Zones under this field
            $zoneIds = Zone::where('field_id', $user->field_id)->pluck('id');
            // Chapters under all zones in this field
            $chapterIds = Chapter::whereIn('zone_id', $zoneIds)->pluck('id');
        }

        // Secretariat → full access (no scoping)
        elseif (in_array($role, secretariatStakeholders())) {
            $chapterIds = Chapter::pluck('id');
            $zoneIds = Zone::pluck('id');
            $fieldIds = Field::pluck('id');
        }

        /** =====================
         * FILTER REPORTS BY SCOPED IDS
         * ===================== */
        $reports = StakeholderReport::query()
            ->when($chapterIds->isNotEmpty(), fn($q) => $q->whereIn('chapter_id', $chapterIds))
            ->when($zoneIds->isNotEmpty(), fn($q) => $q->whereIn('zone_id', $zoneIds))
            ->when($fieldIds->isNotEmpty(), fn($q) => $q->whereIn('field_id', $fieldIds));

        /** =====================
         * DATE FILTERS
         * ===================== */
        if ($request->filled('from_date')) {
            $reports->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $reports->whereDate('created_at', '<=', $request->to_date);
        }

        /** =====================
         * MANUAL FILTERS
         * ===================== */
        if ($request->filled('chapter_filter')) {
            $reports->where('chapter_id', $request->chapter_filter);
        }

        if ($request->filled('zone_filter')) {
            $reports->where('zone_id', $request->zone_filter);
        }

        if ($request->filled('field_filter')) {
            $reports->where('field_id', $request->field_filter);
        }

        /** =====================
         * STATUS FILTERS
         * ===================== */
        if ($request->filled('status_filter')) {
            $statusMap = [
                'field_pending' => ['field_status', 0],
                'field_approved' => ['field_status', 1],
                'field_rejected' => ['field_status', 2],
                'zone_pending' => ['zone_status', 0],
                'zone_approved' => ['zone_status', 1],
                'zone_rejected' => ['zone_status', 2],
                'national_pending' => ['national_status', 0],
                'national_approved' => ['national_status', 1],
                'national_rejected' => ['national_status', 2],
            ];

            if (isset($statusMap[$request->status_filter])) {
                [$column, $value] = $statusMap[$request->status_filter];
                $reports->where($column, $value);
            }
        }

        /** =====================
         * EXECUTE QUERIES
         * ===================== */
        $reports = $reports->with(['chapter', 'zone', 'field'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $chapters = $chapters->whereIn('id', $chapterIds)->orderBy('name')->get();
        $zones = $zones->whereIn('id', $zoneIds)->orderBy('name')->get();
        $fields = $fields->whereIn('id', $fieldIds)->orderBy('name')->get();

        /** =====================
         * REDIRECT FOR FINANCIAL SECRETARY
         * ===================== */
        if ($role === 'Financial Secretary') {
            return redirect(route('stakeholderpayment.index'));
        }
        return view('stakeholder.index', compact('reports', 'chapters', 'fields', 'zones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $months = $this->getMonths();
        $user = Auth::guard('stakeholder')->user();
        $chapter = $user->chapter;

        $sections = StakeholderQuestionSection::isActive()->with([
            'subsections.questions' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('id')->get();

        $prefillData = [
            'chapter_name' => $chapter->name ?? '',
            'month' => date('m'),
            'year' => date('Y'),
            'year_established' => $chapter->year_established ?? '',
            'session' => date('Y') - 1 . '/'. date('Y'),
            'president_name' => '',
        ];
        
        return view('stakeholder.create', compact('months', 'sections', 'prefillData', 'user'));
    }

    
    public function store(Request $request)
    {
        $stakeholder = Auth::guard('stakeholder')->user();

        $validated = $this->validateRequest($request);

        return $this->saveReport($stakeholder, null, $validated);
    }

    public function update(Request $request, StakeholderReport $report)
    {
        $stakeholder = Auth::guard('stakeholder')->user();

        $validated = $this->validateRequest($request);
        
        return $this->saveReport($stakeholder, $report, $validated);
    }

    /**
     * Validate request data.
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'nullable',
            'confirm_information' => 'accepted',
        ]);
    }

    /**
     * Create or update a report.
     * If $report is null → create, else update.
     */
    protected function saveReport($stakeholder, ?StakeholderReport $report, array $validated)
    {
        DB::beginTransaction();
        
        try {
            $isNew = false;
            if (!$report) {
                $report = new StakeholderReport();
                $isNew = true;
            }

            if (in_array($stakeholder->role_id, chapterStakeholders())) {
                $chapter = Chapter::with('zone:id', 'field:id')
                    ->where('id', $stakeholder->chapter_id)
                    ->first();

                if ($chapter && empty($chapter->year_established)) {
                    $chapter->update([
                        'year_established' => $validated['responses']['year_established'] ?? null
                    ]);
                }

                $report->chapter_id = $stakeholder->chapter_id;
                $report->zone_id = $stakeholder->zone_id ?? $chapter?->zone->id;
                $report->field_id = $stakeholder->field_id ?? $chapter?->field->id;
            }

            $report->stakeholder_id = $stakeholder->id;
            $report->session = $validated['responses']['session'] ?? $report->session;
            $report->year = $validated['responses']['year'] ?? $report->year;
            $report->month = $validated['responses']['month'] ?? $report->month;

            $report->save();

            $this->saveResponses($stakeholder, $report, $validated['responses']);

            
            DB::commit();
            
            if(in_array($stakeholder->role_id, chapterStakeholders())){
                ReportNotificationService::handleReportSubmission($report->fresh(), $stakeholder, $isNew ? 'store' : 'update');
            }
            
            $message = $isNew ? 'Report submitted successfully' : 'Report updated successfully';
            return redirect(route('stakeholders.reports.index'))->with('message', $message);
        } catch (\Throwable $e) {
            // dd($e->getMessage().' File:'. $e->getFile().  'Line: '. $e->getLine());
            DB::rollBack();
            return back()->with('error', 'An error occurred while saving the report. ' . $e->getMessage());
        }
    }

    /**
     * Save each response, enforcing question-level permissions.
     */
    protected function saveResponses($stakeholder, StakeholderReport $report, array $responses)
    {
        foreach ($responses as $slug => $answer) {
            $question = StakeholderReportQuestion::where('slug', $slug)->first();
            if (!$question) continue;

            $access = app('App\Services\StakeholderRolePermissionService')
                ->questionAccess($stakeholder, $question);

            if (!$access['edit']) {
                continue;
            }

            $answerValue = is_array($answer) ? json_encode($answer) : $answer;

            StakeholderReportAnswer::updateOrCreate(
                [
                    'report_id' => $report->id,
                    'question_id' => $question->id,
                ],
                [
                    'answer_value' => $answerValue,
                ]
            );
        }
    }

    public function checks($stakeholder){

        $data = [
            'status' => true,
            'message' => 'success'
        ];

        if (
            is_null($stakeholder->signature) ||
            is_null($stakeholder->gen_sec_signature) ||
            is_null($stakeholder->fin_sec_signature) ||
            is_null($stakeholder->evang_sec_signature)
        ) {
            $data = [
                'status' => false,
                'message' => 'Kindly upload signatures first, you will only need to do this once'
            ];
        }

        return $data;
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    public function show(StakeholderReport $report)
    {
        $sections = StakeholderQuestionSection::isActive()->with([
            'subsections.questions' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('id')->get();

        $reportData = $report->answers->mapWithKeys(function ($answer) {
            $decoded = json_decode($answer->answer_value, true);
            return [$answer->question->label => $decoded ?? $answer->answer_value];
        });

        return view('reports.pdf_template', [
            'report'     => $report,
            'reportData' => $reportData,
            'sections'   => $sections
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    public function edit(StakeholderReport $report)
    {
        $report->load('answers'); // eager load answers
        $months = $this->getMonths();
        $user = Auth::guard('stakeholder')->user();
        $chapter = $user->chapter;

        $sections = StakeholderQuestionSection::isActive()->with([
            'subsections.questions' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('id')->get();

        // Only used for static/default fields in the form (chapter info, month/year/session)
        $prefillData = [
            'chapter_name' => $chapter->name ?? '',
            'year_established' => $chapter->year_established ?? '',
            'president_name' => '',
        ];

        // Prepare answers array keyed by question_slug for edit mode
        $answersData = [];
        if ($report->answers) {
            foreach ($report->answers as $answer) {
                $decoded = json_decode($answer->answer, true);
                $answersData[$answer->question_slug] = $decoded ?? $answer->answer;
            }
        }
        
        return view('stakeholder.create', compact('user','months', 'report', 'sections', 'prefillData', 'answersData'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    public function rejectReport(Request $request, StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();
        $report = StakeholderReport::findOrFail($request->report_id);
        $comment = $request->rejection_reason;
        $role = $user->role;

        switch ($role) {
            case 'Zonal Pastor':
                $report->zone_comment = $comment;
                $report->zone_status = 2;
                $report->zone_rejected_at = now();
                // $report->zone_rejected_by = $user->id;
                break;

            case 'Field Pastor':
                if ($report->zone_status !== 1) {
                    abort(403, 'Cannot reject before zone approval');
                }
                $report->field_comment = $comment;
                $report->field_status = 2;
                $report->field_rejected_at = now();
                // $report->field_rejected_by = $user->id;
                break;

            case 'Secretariat':
            case 'NCP':
                if ($report->zone_status !== 1 || $report->field_status !== 1) {
                    abort(403, 'Cannot reject before zone and field approval');
                }
                $report->national_comment = $comment;
                $report->national_status = 2;
                $report->national_rejected_at = now();
                // $report->national_rejected_by = $user->id;
                break;

            default:
                abort(403, 'Unauthorized action');
        }

        $report->save();

        ReportNotificationService::handleReportAction($report, 'reject');

        return redirect()
            ->route('stakeholders.reports.index')
            ->with('message', 'Report rejection recorded successfully!');
    }

    public function approveReport(StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();
        // $comment = $request->rejection_reason ?? null;
        $roleSlug = $user->role->slug;
        
        switch ($roleSlug) {
            case 'zonal-pastor':
                // $report->zone_comment = $comment;
                $report->zone_status = 1; // Approved
                $report->zone_approved_at = now();
                // $report->zone_approved_by = $user->id;
                break;

            case 'field-pastor':
                if ($report->zone_status !== 1) {
                    abort(403, 'Cannot approve before zone approval');
                }
                // $report->field_comment = $comment;
                $report->field_status = 1;
                $report->field_approved_at = now();
                // $report->field_approved_by = $user->id;
                break;

            case 'secretariat':
            case 'ncp':
                if ($report->zone_status !== 1 || $report->field_status !== 1) {
                    abort(403, 'Cannot approve before zone and field approval');
                }
                // $report->national_comment = $comment;
                $report->national_status = 1;
                $report->national_approved_at = now();
                // $report->national_approved_by = $user->id;
                break;

            default:
                abort(403, 'Unauthorized action');
        }

        // $report->save();
        
        ReportNotificationService::handleReportAction($report, 'approve');

        return redirect()
            ->route('stakeholders.reports.index')
            ->with('message', 'Report approved successfully!');
    }


    public function destroy(Reports $reports)
    {
        //
    }

    public function delete($id){
        if(Auth::guard('stakeholder')->user()->role != 'Secretariat') return abort(404);
        $report =  StakeholderReport::find($id);
        if($report->stakeholderpayment){
            if (file_exists(base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image ))
                unlink( base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image );

                $report->stakeholderpayment->delete();
        }
        $report->delete();
       
        return back()->with('message', 'Report has been deleted forever!');
    }

    public function download(StakeholderReport $report): BinaryFileResponse
    {
        $path = $report->file_location;

        abort_unless(file_exists($path), 404, 'Report file not found');

        return response()->download(
            $path,
            basename($path), // filename
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    // private function validateRequestData($request){
    //     $rules = [];

    //     foreach ($sections as $section) {
    //         foreach ($section->subsections as $subsection) {
    //             foreach ($subsection->questions as $question) {
    //                 $field = 'responses.' . $question->slug;

    //                 // Build rules dynamically
    //                 $rules[$field] = $question->is_required ? 'required' : 'nullable';

    //                 // Add type-based validation if quantifiable or specific
    //                 if ($question->type === 'number' || $question->is_quantifiable) {
    //                     $rules[$field] .= '|numeric';
    //                 } elseif ($question->type === 'date') {
    //                     $rules[$field] .= '|date';
    //                 } elseif ($question->type === 'email') {
    //                     $rules[$field] .= '|email';
    //                 }
    //             }
    //         }
    //     }

    //     // Now validate using dynamic rules
    //     $data = $this->validate($request, $rules);

    //     return $data;
    // }
}
