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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role == 1) {
            $subsections = StakeholderQuestionSubSection::withCount('questions')->orderBy('created_at', 'desc')->get();
            return view('admin.stakeholders.subsections.index', compact('subsections'));
        }
        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = StakeholderRole::all();
        $sections = StakeholderQuestionSection::IsActive()->get();
        return view('admin.stakeholders.subsections.edit', compact('roles', 'sections'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $roles = StakeholderRole::all();
        $sections = StakeholderQuestionSection::IsActive()->get();
        $subSection = StakeholderQuestionSubSection::findOrFail($id);

        return view('admin.stakeholders.subsections.edit', compact('subSection', 'roles','sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stakeholder_question_sections,name',
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'exists:stakeholder_roles,id',
            'status' => 'nullable|boolean',
            'section_id' => 'exists:stakeholder_question_sub_sections,id',
        ]);

        StakeholderQuestionSubSection::create([
            'name' => $request->name,
            'access_roles' => $request->access_roles,
            'status' => $request->status ?? 0,
            'section_id' => $request->section_id,
        ]);

        return redirect()->route('stakeholderreportsubsection.index')
            ->with('message', 'Sub Section created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $section = StakeholderQuestionSubSection::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:stakeholder_question_sections,name,' . $section->id,
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'exists:stakeholder_roles,id',
            'status' => 'nullable|boolean',
            'section_id' => 'exists:stakeholder_question_sub_sections,id',
        ]);

        $section->update([
            'name' => $request->name,
            'access_roles' => $request->access_roles,
            'status' => $request->status ?? 0,
            'section_id' => $request->section_id,
        ]);

        return redirect()->route('stakeholderreportsubsection.index')
            ->with('message', 'Section updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $section = StakeholderQuestionSubSection::findOrFail($id);
        $section->delete();

        return redirect()->route('stakeholderreportsubsection.index')
            ->with('message', 'Section deleted successfully.');
    }
}
