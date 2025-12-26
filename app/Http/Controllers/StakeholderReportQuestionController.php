<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StakeholderPermission;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderQuestionSubSection;

class StakeholderReportQuestionController extends Controller
{
    public function index()
    {
        $questions = StakeholderReportQuestion::with('permissions','section')->orderBy('section_id')->orderBy('sub_section_id')->get();
        
        return view('admin.stakeholders.questions.index', compact('questions'));
    }

    public function create()
    {
        $permissions = StakeholderPermission::latest()->get();
        $sections = StakeholderQuestionSection::isActive()->get();
        $subsections = StakeholderQuestionSubSection::isActive()->get();

        return view('admin.stakeholders.questions.edit', compact('sections', 'subsections', 'permissions'));
    }

    public function edit(StakeholderReportQuestion $question)
    {
        $sections = StakeholderQuestionSection::isActive()->get();
        $subsections = StakeholderQuestionSubSection::isActive()->get();
        $permissions = StakeholderPermission::latest()->get();

        return view('admin.stakeholders.questions.edit', compact('question','sections', 'permissions', 'subsections'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'required|string|in:text,number,select,textarea,radio,checkbox,add-entries',
            'is_required' => 'boolean',
            'is_quantifiable' => 'boolean',
            'order' => 'nullable|integer',
            'report_type' => 'nullable|string',
            'section_id' => 'nullable|integer',
            'sub_section_id' => 'nullable|string|max:255',
            'width_class' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'options_keys' => 'nullable|array',
            'options_values' => 'nullable|array',
            'access_permissions' => 'nullable|array',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['label']);
        }

        $options = [];
        if (!empty($validated['options_keys'])) {
            foreach ($validated['options_keys'] as $index => $key) {
                if ($key !== null && $key !== '') {
                    $value = $validated['options_values'][$index] ?? $key;
                    $options[$key] = $value;
                }
            }
        }

        $validated['options'] = $options;
        unset($validated['options_keys'], $validated['options_values'], $validated['access_permissions']);
        
        $question = StakeholderReportQuestion::create($validated);

        // Sync access permissions
        if ($request->has('access_permissions')) {
            $permissions = collect($request->access_permissions)->mapWithKeys(function ($permissionId) {
                $now = Carbon::now();
                return [$permissionId => ['created_at' => $now, 'updated_at' => $now]];
            })->toArray();

            $question->permissions()->sync($permissions);
        }

        return redirect()->route('stakeholder.questions.index')
            ->with('message', 'Question Item created successfully.');
    }

    public function update(Request $request, StakeholderReportQuestion $question)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stakeholder_report_questions,slug,' . $question->id,
            'type' => 'required|string|in:text,number,textarea,select,radio,checkbox,add-entries',
            'is_required' => 'boolean',
            'is_quantifiable' => 'boolean',
            'order' => 'nullable|integer',
            'report_type' => 'nullable|string',
            'section_id' => 'nullable|integer',
            'sub_section_id' => 'nullable|string|max:255',
            'width_class' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'options_keys' => 'nullable|array',
            'options_values' => 'nullable|array',
            'access_permissions' => 'nullable|array',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['label']);
        }

        // Merge options keys and values
        $options = [];
        if (!empty($validated['options_keys'])) {
            foreach ($validated['options_keys'] as $index => $key) {
                if ($key !== null && $key !== '') {
                    $value = $validated['options_values'][$index] ?? $key;
                    $options[$key] = $value;
                }
            }
        }

        $validated['options'] = $options;
        unset($validated['options_keys'], $validated['options_values'], $validated['access_permissions']);

        $question->update($validated);

        // Sync access permissions
        if ($request->has('access_permissions')) {
            $permissions = collect($request->access_permissions)->mapWithKeys(function ($permissionId) {
                $now = Carbon::now();
                return [$permissionId => ['created_at' => $now, 'updated_at' => $now]];
            })->toArray();

            $question->permissions()->sync($permissions);
        } else {
            $question->permissions()->sync([]);
        }

        return redirect()->route('stakeholder.questions.index')
            ->with('message', 'Question updated successfully.');
    }



    public function destroy(StakeholderReportQuestion $question)
    {
        $question->permissions()->sync([]);

        $question->delete();

        return redirect()->route('stakeholder.questions.index')
            ->with('message', 'Question item deleted successfully.');
    }
}
