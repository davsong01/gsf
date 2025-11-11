<?php

namespace App\Http\Controllers;

use App\Models\StakeholderReportQuestion;
use Illuminate\Http\Request;

class StakeholderReportQuestionController extends Controller
{
    public function index()
    {
        $questions = StakeholderReportQuestion::orderBy('section_id')->orderBy('sub_section_id')->get();
        return view('admin.stakeholders.questions.index', compact('questions'));
    }

    public function create()
    {
        $sections = collect([
            (object) ['id' => 1, 'name' => 'SECTION A', 'status' => 'active'],
            (object) ['id' => 2, 'name' => 'SECTION B', 'status' => 'active'],
            (object) ['id' => 3, 'name' => 'SECTION C', 'status' => 'active'],
            (object) ['id' => 4, 'name' => 'SECTION D', 'status' => 'active'],
        ]);
        
        return view('admin.stakeholders.questions.edit', compact('sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_key' => 'required|string|unique:stakeholder_report_questions,question_key',
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,textarea,radio,checkbox',
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'order' => 'nullable|integer',
            'report_type' => 'nullable|string',
            'section' => 'nullable|string',
            'width_class' => 'nullable|string'
        ]);

        StakeholderReportQuestion::create($validated);

        return redirect()->route('stakeholders.questions.index')
            ->with('message', 'Question created successfully.');
    }

    public function edit(StakeholderReportQuestion $question)
    {
        $sections = collect([
            (object) ['id' => 1, 'name' => 'SECTION A', 'status' => 'active'],
            (object) ['id' => 2, 'name' => 'SECTION B', 'status' => 'active'],
            (object) ['id' => 3, 'name' => 'SECTION C', 'status' => 'active'],
            (object) ['id' => 4, 'name' => 'SECTION D', 'status' => 'active'],
        ]);

        return view('admin.stakeholders.questions.edit', compact('question','sections'));
    }

    public function update(Request $request, StakeholderReportQuestion $question)
    {
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.label' => 'required|string|max:255',
            'questions.*.slug' => 'required|string|max:255|unique:stakeholder_report_questions,slug,' . ($question->id ?? 'NULL'),
            'questions.*.type' => 'required|string|in:text,number,textarea,select,radio,checkbox,add-entries',
            'questions.*.is_required' => 'required|boolean',
            'questions.*.options' => 'nullable|array',
            'questions.*.order' => 'nullable|integer',
            'questions.*.section' => 'nullable|string|max:255',
            'width_class' => 'nullable|string'
        ]);
        
        $update = $validated['questions'][0];
        $question->update($update);

        return redirect()->route('stakeholder.questions.index')
            ->with('message', 'Question updated successfully.');
    }

    public function destroy(StakeholderReportQuestion $question)
    {
        $question->delete();
        return redirect()->route('stakeholders.questions.index')
            ->with('message', 'Question deleted successfully.');
    }
}
