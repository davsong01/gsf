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

    public function cloneQuestion(StakeholderReportQuestion $question)
    {
        $clone = $question->replicate();
        $label = 'copy_'.$question->label;
        $clone->label = $label;
        $clone->order = $question->order;
        $clone->section_id = $question->section_id;
        $clone->sub_section_id = $question->sub_section_id;
        $clone->slug = Str::slug($label);
        $clone->status = 0;

        $clone->save();

        return back()->with('message', 'Question Cloned Successfully');
    }


    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'label' => 'required|string|max:255',
    //         'slug' => 'nullable|string|max:255',
    //         'type' => 'required|string|in:text,number,select,textarea,radio,checkbox,add-entries',
    //         'is_required' => 'boolean',
    //         'is_quantifiable' => 'boolean',
    //         'order' => 'nullable|integer',
    //         'report_type' => 'nullable|string',
    //         'section_id' => 'nullable|integer',
    //         'sub_section_id' => 'nullable|string|max:255',
    //         'width_class' => 'nullable|string|max:255',
    //         'status' => 'nullable|boolean',
    //         'options_keys' => 'nullable|array',
    //         'options_values' => 'nullable|array',
    //         'access_permissions' => 'nullable|array',
    //     ]);

    //     if (empty($validated['slug'])) {
    //         $validated['slug'] = Str::slug($validated['label']);
    //     }

    //     $options = [];
    //     if (!empty($validated['options_keys'])) {
    //         foreach ($validated['options_keys'] as $index => $key) {
    //             if ($key !== null && $key !== '') {
    //                 $value = $validated['options_values'][$index] ?? $key;
    //                 $options[$key] = $value;
    //             }
    //         }
    //     }

    //     $validated['options'] = $options;
    //     unset($validated['options_keys'], $validated['options_values'], $validated['access_permissions']);

    //     $question = StakeholderReportQuestion::create($validated);

    //     // Sync access permissions
    //     if ($request->has('access_permissions')) {
    //         $permissions = collect($request->access_permissions)->mapWithKeys(function ($permissionId) {
    //             $now = Carbon::now();
    //             return [$permissionId => ['created_at' => $now, 'updated_at' => $now]];
    //         })->toArray();

    //         $question->permissions()->sync($permissions);
    //     }

    //     return redirect()->route('stakeholder.questions.index')
    //         ->with('message', 'Question Item created successfully.');
    // }

    // public function update(Request $request, StakeholderReportQuestion $question)
    // {
    //     $validated = $request->validate([
    //         'label' => 'required|string|max:255',
    //         'slug' => 'nullable|string|max:255|unique:stakeholder_report_questions,slug,' . $question->id,
    //         'type' => 'required|string|in:text,number,textarea,select,radio,checkbox,rating,dynamic_table,income_table,year,month,date',
    //         'is_required' => 'boolean',
    //         'is_quantifiable' => 'boolean',
    //         'order' => 'nullable|integer',
    //         'report_type' => 'nullable|string',
    //         'section_id' => 'nullable|integer',
    //         'sub_section_id' => 'nullable|string|max:255',
    //         'width_class' => 'nullable|string|max:255',
    //         'status' => 'nullable|boolean',
    //         'options' => 'nullable|array',
    //         'access_permissions' => 'nullable|array',
    //     ]);

    //     // Generate slug if not provided
    //     if (empty($validated['slug'])) {
    //         $validated['slug'] = Str::slug($validated['label']);
    //     }

    //     // Normalize options based on type
    //     $type = $validated['type'];
    //     $options = $request->input('options', []);

    //     if (in_array($type, ['select', 'radio', 'checkbox', 'rating'])) {
    //         // Simple options: label + value
    //         $normalized = [];
    //         foreach ($options as $opt) {
    //             if (!empty($opt['label']) && isset($opt['value'])) {
    //                 $normalized[] = [
    //                     'label' => $opt['label'],
    //                     'value' => $opt['value'],
    //                 ];
    //             }
    //         }
    //         $validated['options'] = $normalized;
    //     } elseif ($type === 'dynamic_table') {
    //         // Complex table options: label, type, required, quantifiable
    //         $normalized = [];
    //         foreach ($options as $opt) {
    //             if (!empty($opt['label'])) {
    //                 $normalized[] = [
    //                     'label' => $opt['label'],
    //                     'type' => $opt['type'] ?? 'text',
    //                     'required' => !empty($opt['required']),
    //                     'is_quantifiable' => !empty($opt['is_quantifiable']),
    //                 ];
    //             }
    //         }
    //         $validated['options'] = $normalized;
    //     } elseif ($type === 'income_table') {
    //         // Income table: must have rows and columns
    //         $columns = $options['columns'] ?? [];
    //         $rows = $options['rows'] ?? [];
    //         $validated['options'] = [
    //             'columns' => array_values(array_filter($columns)),
    //             'rows' => array_values(array_filter($rows)),
    //         ];
    //     } else {
    //         // Fallback: save whatever array is provided
    //         $validated['options'] = $options;
    //     }

    //     // Remove access_permissions so it doesn't get mass-assigned
    //     unset($validated['access_permissions']);

    //     $question->update($validated);

    //     // Sync access permissions
    //     if ($request->has('access_permissions')) {
    //         $permissions = collect($request->access_permissions)->mapWithKeys(function ($permissionId) {
    //             $now = Carbon::now();
    //             return [$permissionId => ['created_at' => $now, 'updated_at' => $now]];
    //         })->toArray();

    //         $question->permissions()->sync($permissions);
    //     } else {
    //         $question->permissions()->sync([]);
    //     }

    //     return redirect()->route('stakeholder.questions.index')
    //         ->with('message', 'Question updated successfully.');
    // }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);

        $question = StakeholderReportQuestion::create($validated);

        $this->syncPermissions($question, $request->input('access_permissions', []));

        return redirect()->route('stakeholder.questions.index')
            ->with('message', 'Question created successfully.');
    }

    public function update(Request $request, StakeholderReportQuestion $question)
    {
        $validated = $this->validateQuestion($request, $question->id);
        $validated['slug'] = Str::slug($validated['label']);

        $question->update($validated);

        $this->syncPermissions($question, $request->input('access_permissions', []));

        return redirect()->route('stakeholder.questions.index')
            ->with('message', 'Question updated successfully.');
    }

    /**
     * Validate and normalize question input.
     */
    protected function validateQuestion(Request $request, $questionId = null): array
    {
        $rules = [
            'label' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stakeholder_report_questions,slug,' . $questionId,
            'type' => 'required|string|in:text,number,textarea,select,radio,checkbox,rating,dynamic_table,income_table,year,month,date,file',
            'is_required' => 'boolean',
            'is_quantifiable' => 'boolean',
            'order' => 'nullable|integer',
            'report_type' => 'nullable|string',
            'section_id' => 'nullable|integer',
            'sub_section_id' => 'nullable|string|max:255',
            'width_class' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'options' => 'nullable|array',
        ];

        $validated = $request->validate($rules);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['label']);
        }

        // Normalize options
        $validated['options'] = $this->normalizeOptions($validated['type'], $request->input('options', []));

        return $validated;
    }

    /**
     * Normalize options based on type.
     */
    protected function normalizeOptions(string $type, array $options): array
    {
        if (in_array($type, ['select', 'radio', 'checkbox', 'rating'])) {
            $normalized = [];
            foreach ($options as $opt) {
                if (!empty($opt['label']) && isset($opt['value'])) {
                    $normalized[] = [
                        'label' => $opt['label'],
                        'value' => $opt['value'],
                    ];
                }
            }
            return $normalized;
        }

        if ($type === 'dynamic_table') {
            $normalized = [];
            foreach ($options as $opt) {
                if (!empty($opt['label'])) {
                    $normalized[] = [
                        'label' => $opt['label'],
                        'type' => $opt['type'] ?? 'text',
                        'required' => !empty($opt['required']),
                        'is_quantifiable' => !empty($opt['is_quantifiable']),
                    ];
                }
            }
            return $normalized;
        }

        if ($type === 'income_table') {
            return [
                'columns' => array_values(array_filter($options['columns'] ?? [])),
                'rows' => array_values(array_filter($options['rows'] ?? [])),
            ];
        }

        // Default: just return whatever array was provided
        return $options;
    }

    /**
     * Sync access permissions for a question.
     */
    protected function syncPermissions(StakeholderReportQuestion $question, array $permissions): void
    {
        $syncData = collect($permissions)->mapWithKeys(function ($permissionId) {
            $now = Carbon::now();
            return [$permissionId => ['created_at' => $now, 'updated_at' => $now]];
        })->toArray();

        $question->permissions()->sync($syncData);
    }

    public function destroy(StakeholderReportQuestion $question)
    {
        $question->permissions()->sync([]);

        $question->delete();

        return redirect()->route('stakeholder.questions.index')
            ->with('message', 'Question item deleted successfully.');
    }
}
