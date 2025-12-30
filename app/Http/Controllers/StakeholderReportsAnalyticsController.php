<?php

namespace App\Http\Controllers\Stakeholders;

use App\Http\Controllers\Controller;
use App\Models\StakeholderReport;
use App\Models\StakeholderReportAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StakeholderReportsAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $questions = Question::where('quantifiable', 1)->get();

        return view('stakeholders.analytics.index', compact('questions'));
    }

    public function data(Request $request)
    {
        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date'],
        ]);

        $answers = StakeholderReportAnswer::query()
            ->select([
                'chapters.name as chapter',
                DB::raw('SUM(stakeholder_report_answers.answer) as total'),
                DB::raw('DATE(stakeholder_reports.created_at) as date'),
            ])
            ->join('stakeholder_reports', 'stakeholder_reports.id', '=', 'stakeholder_report_answers.stakeholder_report_id')
            ->join('chapters', 'chapters.id', '=', 'stakeholder_reports.chapter_id')
            ->where('stakeholder_report_answers.question_id', $validated['question_id'])
            ->whereNotNull('stakeholder_report_answers.answer')
            ->when(
                $validated['from'] ?? null,
                fn($q, $from) =>
                $q->whereDate('stakeholder_reports.created_at', '>=', $from)
            )
            ->when(
                $validated['to'] ?? null,
                fn($q, $to) =>
                $q->whereDate('stakeholder_reports.created_at', '<=', $to)
            )
            ->groupBy('chapters.name', 'date')
            ->orderBy('date')
            ->get();

        return response()->json(
            $this->transformForChart($answers)
        );
    }

    protected function transformForChart($rows): array
    {
        $dates = $rows->pluck('date')->unique()->values();

        $datasets = $rows
            ->groupBy('chapter')
            ->map(function ($group, $chapter) use ($dates) {
                return [
                    'label' => $chapter,
                    'data'  => $dates->map(
                        fn($date) => (float) ($group->firstWhere('date', $date)->total ?? 0)
                    ),
                    'fill' => false,
                ];
            })
            ->values();

        return [
            'labels'   => $dates,
            'datasets' => $datasets,
        ];
    }
}
