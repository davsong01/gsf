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
        return $request->input('module_type', $request->query('module_type', 'report')) === 'appraisal'
            ? 'appraisal'
            : 'report';
    }

    protected function moduleFilter(Request $request): string
    {
        return in_array($request->query('module_type'), ['report', 'appraisal'], true)
            ? $request->query('module_type')
            : 'all';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $moduleType = $this->moduleFilter($request);
        $name = trim((string) $request->query('name', ''));
        $status = $request->query('status');
        $permission = $request->query('permission');
        $sectionId = $request->query('section_id');
        if (auth()->user()->role == 1) {
            $subsectionsQuery = StakeholderQuestionSubSection::query()
                ->with(['section'])
                ->withCount('questions')
                ->orderBy('created_at', 'desc');

            if ($moduleType !== 'all') {
                $subsectionsQuery->forModule($moduleType);
            }

            if ($name !== '') {
                $subsectionsQuery->where('name', 'like', '%' . $name . '%');
            }

            if (in_array($status, ['0', '1'], true)) {
                $subsectionsQuery->where('status', (int) $status);
            }

            if ($permission !== null && $permission !== '') {
                $subsectionsQuery->whereJsonContains('access_roles', (int) $permission);
            }

            if ($sectionId !== null && $sectionId !== '') {
                $subsectionsQuery->where('section_id', (int) $sectionId);
            }

            $subsections = $subsectionsQuery->paginate(30)->withQueryString();
            $roles = StakeholderRole::orderBy('name')->get();
            $sections = StakeholderQuestionSection::query()
                ->when($moduleType !== 'all', fn ($query) => $query->forModule($moduleType))
                ->orderBy('name')
                ->get();

            return view('admin.stakeholders.subsections.index', compact('subsections', 'moduleType', 'roles', 'sections'));
        }
        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $moduleType = $this->moduleType($request);
        $permissions = $this->permissionsForModule($moduleType);
        $sections = StakeholderQuestionSection::IsActive()
            ->forModule($moduleType)
            ->get();
        return view('admin.stakeholders.subsections.edit', compact('permissions', 'sections', 'moduleType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $subSection = StakeholderQuestionSubSection::findOrFail($id);
        $moduleType = $request->query('module_type') ?: ($subSection->module_type ?? 'report');
        $permissions = $this->permissionsForModule($moduleType);
        $sections = StakeholderQuestionSection::IsActive()
            ->forModule($moduleType)
            ->get();

        return view('admin.stakeholders.subsections.edit', compact('subSection', 'permissions', 'sections', 'moduleType'));
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

        $permissionTable = $moduleType === 'appraisal'
            ? 'stakeholder_permissions'
            : 'stakeholder_roles';
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stakeholder_question_sub_sections,slug,NULL,id,module_type,' . $moduleType,
            'module_type' => 'required|in:report,appraisal',
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'integer|exists:' . $permissionTable . ',id',
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

        $permissionTable = $moduleType === 'appraisal'
            ? 'stakeholder_permissions'
            : 'stakeholder_roles';

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stakeholder_question_sub_sections,slug,' . $section->id . ',id,module_type,' . $moduleType,
            'module_type' => 'required|in:report,appraisal',
            'access_roles' => 'nullable|array',
            'access_roles.*' => 'integer|exists:' . $permissionTable . ',id',
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

    protected function permissionsForModule(string $moduleType)
    {
        if ($moduleType === 'appraisal') {
            $allowed = [
                'field-pastor-fill',
                'zonal-pastor-fill',
                'nec-member-fill',
                'nec-member-evaluate',
                'field-pastor-evaluate',
                'national-president-fill',
                'national-president-evaluate',
                'ncp-evaluate',
            ];

            return StakeholderPermission::query()
                ->whereIn('slug', $allowed)
                ->orderBy('name')
                ->get();
        }

        return StakeholderRole::query()->orderBy('name')->get();
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
