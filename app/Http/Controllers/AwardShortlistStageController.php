<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AwardShortlistStage;
use App\Http\Controllers\Controller;
use App\Services\HttpResponseService;
use App\Http\Requests\AwardShortlistStageRequest;
use App\Http\Resources\AwardShortlistStageResource;

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

        $stages = AwardShortlistStage::query();

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

        // Handle structural boolean parameters explicitly if absent from standard input structures
        $validated['active'] = $request->has('active');
        $validated['mark_as_final'] = $request->has('mark_as_final');

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

        $validated['active'] = $request->has('active');
        $validated['mark_as_final'] = $request->has('mark_as_final');

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
}
