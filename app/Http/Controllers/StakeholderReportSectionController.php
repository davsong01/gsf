<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderPermission;
use App\Models\StakeholderRole;

class StakeholderReportSectionController extends Controller
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
            $sections = StakeholderQuestionSection::forModule($moduleType)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.stakeholders.sections.index', compact('sections', 'moduleType'));
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
        return view('admin.stakeholders.sections.edit', compact('roles', 'moduleType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $section = StakeholderQuestionSection::findOrFail($id);
        $moduleType = $request->query('module_type') ?: ($section->module_type ?? 'report');
        $roles = StakeholderRole::all();
        return view('admin.stakeholders.sections.edit', compact('section', 'roles', 'moduleType'));
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
            'slug' => 'required|string|max:255|unique:stakeholder_question_sections,slug,NULL,id,module_type,' . $moduleType,
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'exists:stakeholder_roles,id',
            'status' => 'nullable|boolean',
        ]);
        
        StakeholderQuestionSection::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'module_type' => $moduleType,
            'access_roles' => $request->access_roles,
            'status' => $request->status ?? 0,
        ]);

        return redirect()->route('stakeholderreportsection.index', ['module_type' => $moduleType])
            ->with('message', 'Section created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $section = StakeholderQuestionSection::findOrFail($id);
        $moduleType = $this->moduleType($request) ?: ($section->module_type ?? 'report');
        $request->merge([
            'slug' => Str::slug($request->slug ?: $request->name),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stakeholder_question_sections,slug,' . $section->id . ',id,module_type,' . $moduleType,
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'exists:stakeholder_roles,id',
            'status' => 'nullable|boolean',
        ]);

        $section->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'module_type' => $moduleType,
            'access_roles' => $request->access_roles,
            'status' => $request->status ?? 0,
        ]);

        return redirect()->route('stakeholderreportsection.index', ['module_type' => $moduleType])
            ->with('message', 'Section updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $section = StakeholderQuestionSection::findOrFail($id);
        $moduleType = $section->module_type ?? 'report';
        $section->delete();

        return redirect()->route('stakeholderreportsection.index', ['module_type' => $moduleType])
            ->with('message', 'Section deleted successfully.');
    }
}
