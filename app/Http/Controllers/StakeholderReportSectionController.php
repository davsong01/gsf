<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderPermission;
use App\Models\StakeholderRole;

class StakeholderReportSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role == 1) {
            $sections = StakeholderQuestionSection::orderBy('created_at', 'desc')->get();
            return view('admin.stakeholders.sections.index', compact('sections'));
        }
        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = StakeholderRole::all();
        return view('admin.stakeholders.sections.edit', compact('roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $section = StakeholderQuestionSection::findOrFail($id);
        $roles = StakeholderRole::all();
        return view('admin.stakeholders.sections.edit', compact('section', 'roles'));
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
        ]);
        
        StakeholderQuestionSection::create([
            'name' => $request->name,
            'access_roles' => $request->access_roles,
            'status' => $request->status ?? 0,
        ]);

        return redirect()->route('stakeholderreportsection.index')
            ->with('message', 'Section created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $section = StakeholderQuestionSection::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:stakeholder_question_sections,name,' . $section->id,
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'exists:stakeholder_roles,id',
            'status' => 'nullable|boolean',
        ]);

        $section->update([
            'name' => $request->name,
            'access_roles' => $request->access_roles,
            'status' => $request->status ?? 0,
        ]);

        return redirect()->route('stakeholderreportsection.index')
            ->with('message', 'Section updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $section = StakeholderQuestionSection::findOrFail($id);
        $section->delete();

        return redirect()->route('stakeholderreportsection.index')
            ->with('message', 'Section deleted successfully.');
    }
}
