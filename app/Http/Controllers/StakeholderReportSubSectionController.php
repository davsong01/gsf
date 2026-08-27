<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StakeholderRole;
use App\Models\StakeholderPermission;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderQuestionSubSection;

class StakeholderReportSubSectionController extends Controller
{
    protected function moduleType(Request $request): string
    {
        return $request->query('module_type', 'report') === 'appraisal'
            ? 'appraisal'
            : 'report';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $moduleType = $this->moduleType($request);
        if (auth()->user()->role == 1) {
            $subsections = StakeholderQuestionSubSection::forModule($moduleType)
                ->withCount('questions')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.stakeholders.subsections.index', compact('subsections', 'moduleType'));
        }
        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $moduleType = $this->moduleType($request);
        $roles = StakeholderRole::all();
        $sections = StakeholderQuestionSection::IsActive()
            ->forModule($moduleType)
            ->get();
        return view('admin.stakeholders.subsections.edit', compact('roles', 'sections', 'moduleType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $roles = StakeholderRole::all();
        $subSection = StakeholderQuestionSubSection::findOrFail($id);
        $moduleType = $request->query('module_type') ?: ($subSection->module_type ?? 'report');
        $sections = StakeholderQuestionSection::IsActive()
            ->forModule($moduleType)
            ->get();

        return view('admin.stakeholders.subsections.edit', compact('subSection', 'roles', 'sections', 'moduleType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $moduleType = $this->moduleType($request);
        $request->merge([
            'slug' => Str::slug($request->slug ?: $request->name),
        ]);
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stakeholder_question_sub_sections,slug,NULL,id,module_type,' . $moduleType,
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'exists:stakeholder_roles,id',
            'status' => 'nullable|boolean',
            'section_id' => 'exists:stakeholder_question_sections,id',
        ]);

        StakeholderQuestionSubSection::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'module_type' => $moduleType,
            'access_roles' => $request->access_roles,
            'status' => $request->status ?? 0,
            'section_id' => $request->section_id,
        ]);

        return redirect()->route('stakeholderreportsubsection.index', ['module_type' => $moduleType])
            ->with('message', 'Sub Section created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $section = StakeholderQuestionSubSection::findOrFail($id);
        $moduleType = $this->moduleType($request) ?: ($section->module_type ?? 'report');
        $request->merge([
            'slug' => Str::slug($request->slug ?: $request->name),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stakeholder_question_sub_sections,slug,' . $section->id . ',id,module_type,' . $moduleType,
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'exists:stakeholder_roles,id',
            'status' => 'nullable|boolean',
            'section_id' => 'exists:stakeholder_question_sections,id',
        ]);

        $section->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'module_type' => $moduleType,
            'access_roles' => $request->access_roles,
            'status' => $request->status ?? 0,
            'section_id' => $request->section_id,
        ]);

        return redirect()->route('stakeholderreportsubsection.index', ['module_type' => $moduleType])
            ->with('message', 'Section updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $section = StakeholderQuestionSubSection::findOrFail($id);
        $moduleType = $section->module_type ?? 'report';
        $section->delete();

        return redirect()->route('stakeholderreportsubsection.index', ['module_type' => $moduleType])
            ->with('message', 'Section deleted successfully.');
    }
}
