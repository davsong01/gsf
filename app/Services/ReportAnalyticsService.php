<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\Field;
use App\Models\StakeholderReport;
use App\Models\Zone;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportAnalyticsService
{
    public function fetchAnalyticsTypeData(Request $request, $scope = [])
    {
        $groupBy = $request->group_by ?? 'chapter';

        $statusLevels = [
            0 => 'Not Submitted',
            1 => 'Currently Editing',
            2 => 'Submitted',
            3 => 'Zone Rejected',
            4 => 'Zone Approved',
            5 => 'Field Rejected',
            6 => 'Field Approved',
            7 => 'National Rejected',
            8 => 'National Approved',
        ];

        $from = $request->from_date
            ? Carbon::parse($request->from_date)->startOfMonth()
            : Carbon::now()->startOfMonth();

        $to = $request->to_date
            ? Carbon::parse($request->to_date)->endOfMonth()
            : Carbon::now()->endOfMonth();

        $months = [];
        for ($date = $from->copy(); $date <= $to; $date->addMonth()) {
            $months[] = $date->format('Y-m');
        }

        $availableFieldIds = $scope['fieldIds']->toArray() ?? [];
        $availableZoneIds = $scope['zoneIds']->toArray() ?? [];

        // Apply user filters but intersect with available scope
        $fieldFilter = $request->filled('fields')
            ? array_intersect($request->fields, $availableFieldIds)
            : $availableFieldIds;

        $zoneFilter = $request->filled('zones')
            ? array_intersect($request->zones, $availableZoneIds)
            : $availableZoneIds;

        $datasets = [];

        if ($groupBy === 'chapter') {
            $chapters = Chapter::query()
                ->when($fieldFilter, fn ($q) => $q->whereIn('field_id', $fieldFilter))
                ->when($zoneFilter, fn ($q) => $q->whereIn('zone_id', $zoneFilter))
                ->orderBy('name')
                ->get();

            foreach ($chapters as $chapter) {

                $data = [];
                $tooltips = [];

                foreach ($months as $month) {
                    [$year, $m] = explode('-', $month);

                    $report = StakeholderReport::where([
                        'chapter_id' => $chapter->id,
                        'year' => $year,
                        'month' => $m,
                    ])->first();

                    $status = 0;
                    if ($report) {
                        if ($report->national_rejected_at) {
                            $status = 7;
                        } elseif ($report->national_status) {
                            $status = 8;
                        } elseif ($report->field_rejected_at) {
                            $status = 5;
                        } elseif ($report->field_status) {
                            $status = 6;
                        } elseif ($report->zone_rejected_at) {
                            $status = 3;
                        } elseif ($report->zone_status) {
                            $status = 4;
                        } elseif ($report->edit_mode) {
                            $status = 1;
                        } else {
                            $status = 2;
                        }
                    }

                    $data[] = $status;
                    $tooltips[] = [[
                        'chapter_name' => $chapter->name,
                        'status' => $status,
                        'status_label' => $statusLevels[$status],
                    ]];
                }

                $datasets[] = [
                    'legend_id' => $chapter->id,
                    'label' => $chapter->name,
                    'data' => $data,
                    'tooltip' => $tooltips,
                    'borderWidth' => 1.5,
                    'tension' => 0.3,
                    'fill' => false,
                ];
            }

        } else {

            if ($groupBy === 'field') {
                $groups = Field::query()
                    ->when($fieldFilter, fn ($q) => $q->whereIn('id', $fieldFilter))
                    ->orderBy('name')
                    ->get();
            } else {
                $groups = Zone::query()
                    ->when($zoneFilter, fn ($q) => $q->whereIn('id', $zoneFilter))
                    ->when($fieldFilter, fn ($q) => $q->whereIn('field_id', $fieldFilter))
                    ->orderBy('name')
                    ->get();
            }

            foreach ($groups as $group) {

                $chaptersInGroup = Chapter::query()
                    ->when($groupBy === 'field', fn ($q) => $q->where('field_id', $group->id))
                    ->when($groupBy === 'zone', fn ($q) => $q->where('zone_id', $group->id))
                    ->when($fieldFilter, fn ($q) => $q->whereIn('field_id', $fieldFilter))
                    ->when($zoneFilter, fn ($q) => $q->whereIn('zone_id', $zoneFilter))
                    ->get();

                $data = [];
                $tooltips = [];

                foreach ($months as $month) {
                    [$year, $m] = explode('-', $month);

                    $maxStatus = 0;
                    $chapterStatuses = [];

                    foreach ($chaptersInGroup as $chapter) {

                        $report = StakeholderReport::where([
                            'chapter_id' => $chapter->id,
                            'year' => $year,
                            'month' => $m,
                        ])->first();

                        $status = 0;
                        if ($report) {
                            if ($report->national_rejected_at) {
                                $status = 7;
                            } elseif ($report->national_status) {
                                $status = 8;
                            } elseif ($report->field_rejected_at) {
                                $status = 5;
                            } elseif ($report->field_status) {
                                $status = 6;
                            } elseif ($report->zone_rejected_at) {
                                $status = 3;
                            } elseif ($report->zone_status) {
                                $status = 4;
                            } elseif ($report->edit_mode) {
                                $status = 1;
                            } else {
                                $status = 2;
                            }
                        }

                        $chapterStatuses[] = [
                            'chapter_name' => $chapter->name,
                            'status' => $status,
                            'status_label' => $statusLevels[$status],
                        ];

                        if ($status > $maxStatus) {
                            $maxStatus = $status;
                        }
                    }

                    $data[] = $maxStatus;
                    $tooltips[] = $chapterStatuses;
                }

                $datasets[] = [
                    'legend_id' => $group->id,
                    'label' => $group->name,
                    'data' => $data,
                    'tooltip' => $tooltips,
                    'borderWidth' => 1.5,
                    'tension' => 0.3,
                    'fill' => false,
                ];
            }
        }

        $labels = collect($months)
            ->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))
            ->toArray();

        return [
            'labels' => $labels,
            'datasets' => $datasets,
            'status_levels' => $statusLevels,
        ];
    }

    public function generateSubmissionStatusReport($scope, $from = null, $to = null): array
    {
        $query = DB::table('stakeholder_reports')->where('edit_mode', 0);

        // =========================
        // DATE RANGE FILTER
        // =========================
        $start = $from
            ? Carbon::parse($from)->startOfMonth()
            : Carbon::create(date('Y'), 1, 1);

        $end = $to
            ? Carbon::parse($to)->endOfMonth()
            : now()->endOfMonth();

        $query->whereBetween('created_at', [$start, $end]);

        $reports = $query->get();

        $groupedRows = [];
        $periods = [];

        foreach ($reports as $report) {

            $period = date(
                'M Y',
                mktime(0, 0, 0, (int) $report->month, 1, (int) $report->year)
            );

            $periods[$period] = $period;

            foreach ($report->answers as $answer) {

                $key = implode('|', [
                    $report->chapter_id,
                    $report->zone_id,
                    $report->field_id,
                    $answer->question_section_id,
                    $answer->question_sub_section_id,
                    $answer->question_id,
                ]);

                if (! isset($groupedRows[$key])) {

                    $groupedRows[$key] = [
                        'Chapter' => $report->chapter->name ?? '-',
                        'Zone' => $report->zone->name ?? '-',
                        'Field' => $report->field->name ?? '-',
                        'Section' => $answer->section->name ?? '-',
                        'Sub Section' => $answer->subSection->name ?? '-',
                        'Question' => $answer->question->label ?? '-',
                    ];
                }

                $groupedRows[$key][$period] = $this->normalizeAnswerValue(
                    $answer->answer_value ?? $answer->answer
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sort periods chronologically
        |--------------------------------------------------------------------------
        */
        uksort($periods, function ($a, $b) {
            return strtotime('01 '.$a) <=> strtotime('01 '.$b);
        });

        $periods = array_values($periods);

        /*
        |--------------------------------------------------------------------------
        | Ensure every row has every month column
        |--------------------------------------------------------------------------
        */
        $rows = [];

        foreach ($groupedRows as $row) {

            foreach ($periods as $period) {
                $row[$period] = $row[$period] ?? '-';
            }

            $rows[] = $row;
        }

        return [
            'headers' => array_merge(
                [
                    'Chapter',
                    'Zone',
                    'Field',
                    'Section',
                    'Sub Section',
                    'Question',
                ],
                $periods
            ),
            'rows' => $rows,
        ];
    }

    protected function getNeverSubmitted($reports, $chapters, $fields): array
    {
        // Build a fast lookup of chapters that HAVE reports in the period
        $hasReport = [];

        foreach ($reports as $r) {
            $hasReport[$r->chapter_id] = true;
        }

        $result = [];

        foreach ($chapters as $chapter) {

            // if chapter has at least one report → skip
            if (isset($hasReport[$chapter->id])) {
                continue;
            }

            $field = $fields[$chapter->field_id] ?? null;
            if (! $field) {
                continue;
            }

            $result[$field->name][] = $chapter->name;
        }

        return $result;
    }

    protected function group($reports, $chapters, $fields, callable $filter): array
    {
        $result = [];

        foreach ($reports as $r) {

            if (! $filter($r)) {
                continue;
            }

            $month = $this->normalizeMonth($r->month, $r->year);
            if (! $month) {
                continue;
            }

            $chapter = $chapters[$r->chapter_id] ?? null;
            $field = $fields[$r->field_id] ?? null;

            if (! $chapter || ! $field) {
                continue;
            }

            $result[$field->name][$chapter->name][] = $month;
        }

        return $this->uniqueMonths($result);
    }

    protected function normalizeMonth($month, $year = null): ?string
    {
        if (! $month) {
            return null;
        }

        if (is_numeric($month) && $year) {
            return Carbon::createFromDate((int) $year, (int) $month, 1)
                ->format('Y-m');
        }

        if (preg_match('/^\d{4}-\d{2}$/', $month)) {
            return Carbon::createFromFormat('Y-m', $month)->format('Y-m');
        }

        // fallback
        try {
            return Carbon::parse($month)->format('Y-m');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function formatMonthForDisplay(string $ym): string
    {
        try {
            return Carbon::createFromFormat('Y-m', $ym)->format('F Y');
        } catch (\Exception $e) {
            return $ym;
        }
    }

    protected function getDefaulters($reports, $chapters, $fields, $from, $to): array
    {
        $expectedMonths = $this->getMonthRange($from, $to);

        /**
         * Build fast lookup:
         * chapter_id => month => true
         */
        $reportMap = [];

        foreach ($reports as $r) {

            $month = $this->normalizeMonth($r->month, $r->year);
            if (! $month) {
                continue;
            }

            $reportMap[$r->chapter_id][$month] = true;
        }

        $result = [];

        foreach ($chapters as $chapter) {

            $field = $fields[$chapter->field_id] ?? null;
            if (! $field) {
                continue;
            }

            foreach ($expectedMonths as $month) {

                if (! isset($reportMap[$chapter->id][$month])) {

                    $result[$field->name][$chapter->name][] = $month;
                }
            }
        }

        return $result;
    }

    protected function getMonthRange($from, $to): array
    {
        $start = $from
            ? Carbon::parse($from)->startOfMonth()
            : Carbon::create(date('Y'), 1, 1);

        $end = $to
            ? Carbon::parse($to)->endOfMonth()
            : now()->endOfMonth();

        /**
         * If today is before the 25th,
         * exclude the current month from reporting.
         */
        if (now()->day < 25) {
            $currentMonth = now()->format('Y-m');

            if ($end->format('Y-m') >= $currentMonth) {
                $end = now()->subMonth()->endOfMonth();
            }
        }

        $months = [];

        while ($start->lte($end)) {
            $months[] = $start->format('Y-m');
            $start->addMonth();
        }

        return $months;
    }

    protected function uniqueMonths(array $data): array
    {
        foreach ($data as $field => $chapters) {

            foreach ($chapters as $chapter => $months) {

                // remove duplicates
                $months = array_values(array_unique($months));

                // sort months in calendar order (optional but recommended)
                usort($months, function ($a, $b) {
                    return strtotime($a) <=> strtotime($b);
                });

                $data[$field][$chapter] = $months;
            }
        }

        return $data;
    }

    public function downloadSubmissionStatusPdf(array $data, Request $request)
    {
        $pdf = Pdf::loadView(
            'admin.reports.analytics.export-pdf',
            [
                'type' => $request->type ?? 'GSF REPORT',
                'from' => $request->from_date,
                'to' => $request->to_date,

                // MAIN DATA CONTRACT
                'nationallyApproved' => $data['nationallyApproved'] ?? [],
                'nationalDeclined' => $data['nationalDeclined'] ?? [],
                'nationalPending' => $data['nationalPending'] ?? [],

                'zoneApproved' => $data['zoneApproved'] ?? [],
                'pendingZoneApproval' => $data['pendingZoneApproval'] ?? [],
                'zoneDeclined' => $data['zoneDeclined'] ?? [],

                'fieldApproved' => $data['fieldApproved'] ?? [],
                'pendingFieldApproval' => $data['pendingFieldApproval'] ?? [],
                'fieldDeclined' => $data['fieldDeclined'] ?? [],

                'monthsYetToSubmit' => $data['monthsYetToSubmit'] ?? [],
                // 'neverSubmitted'      => $data['neverSubmitted'] ?? [],
            ]
        )
            ->setPaper('a4', 'portrait');

        return $pdf->download('gsf_monthly_report.pdf');
    }

    // public function getQuestionAnalysisData(Request $request): array
    // {
    //     $query = StakeholderReport::query()
    //         ->with([
    //             'stakeholder',
    //             'chapter',
    //             'zone',
    //             'field',
    //             'answers.section',
    //             'answers.subSection',
    //             'answers.question'
    //         ]);

    //     if ($request->filled('fields')) {
    //         $query->whereIn('field_id', $request->fields);
    //     }

    //     if ($request->filled('zones')) {
    //         $query->whereIn('zone_id', $request->zones);
    //     }

    //     if ($request->filled('chapters')) {
    //         $query->whereIn('chapter_id', $request->chapters);
    //     }

    //     if ($request->filled('sections') && !in_array('all', (array) $request->sections)) {

    //         $query->whereHas('answers', function ($q) use ($request) {
    //             $q->whereIn('question_section_id', $request->sections);
    //         });

    //         $query->with([
    //             'answers' => function ($q) use ($request) {
    //                 $q->whereIn('question_section_id', $request->sections);
    //             }
    //         ]);
    //     }

    //     if ($request->filled('sub_sections') && !in_array('all', (array) $request->sub_sections)) {

    //         $query->whereHas('answers', function ($q) use ($request) {
    //             $q->whereIn('question_sub_section_id', $request->sub_sections);
    //         });

    //         $query->with([
    //             'answers' => function ($q) use ($request) {
    //                 $q->whereIn('question_sub_section_id', $request->sub_sections);
    //             }
    //         ]);
    //     }

    //     if ($request->filled('from_date')) {
    //         $query->whereDate('created_at', '>=', $request->from_date);
    //     }

    //     if ($request->filled('to_date')) {
    //         $query->whereDate('created_at', '<=', $request->to_date);
    //     }

    //     $reports = $query->get();

    //     $periods = [];
    //     $groupedRows = [];

    //     foreach ($reports as $report) {

    //         $period = Carbon::create(
    //             (int) $report->year,
    //             (int) $report->month,
    //             1
    //         )->format('M Y');

    //         $periods[$period] = Carbon::create(
    //             (int) $report->year,
    //             (int) $report->month,
    //             1
    //         )->timestamp;

    //         foreach ($report->answers as $answer) {

    //             $key = implode('|', [
    //                 $report->chapter_id,
    //                 $report->zone_id,
    //                 $report->field_id,
    //                 $answer->question_section_id,
    //                 $answer->question_sub_section_id,
    //                 $answer->question_id,
    //             ]);

    //             if (!isset($groupedRows[$key])) {

    //                 $groupedRows[$key] = [
    //                     'Chapter'      => $report->chapter->name ?? '-',
    //                     // 'Zone'         => $report->zone->name ?? '-',
    //                     // 'Field'        => $report->field->name ?? '-',
    //                     // 'Section'      => $answer->section->name ?? '-',
    //                     // 'Sub Section'  => $answer->subSection->name ?? '-',
    //                     'Item'     => $answer->question->label ?? '-',
    //                 ];
    //             }

    //             $groupedRows[$key][$period] = $this->normalizeAnswerValue(
    //                 $answer->answer_value ?? $answer->answer
    //             );
    //         }
    //     }

    //     asort($periods);
    //     $periodColumns = array_keys($periods);

    //     $rows = [];

    //     foreach ($groupedRows as $row) {

    //         foreach ($periodColumns as $period) {
    //             $row[$period] = $row[$period] ?? '-';
    //         }

    //         $rows[] = $row;
    //     }

    //     return [
    //         'headers' => array_merge(
    //             [
    //                 'Chapter',
    //                 // 'Zone',
    //                 // 'Field',
    //                 // 'Section',
    //                 // 'Sub Section',
    //                 'Item',
    //             ],
    //             $periodColumns
    //         ),
    //         'rows' => $rows,
    //     ];
    // }

    // private function normalizeAnswerValue($value): string
    // {
    //     if (is_null($value)) {
    //         return '-';
    //     }

    //     if (is_string($value) || is_numeric($value)) {
    //         return (string) $value;
    //     }

    //     if (is_array($value)) {

    //         // LIST OF OBJECTS (repeater type)
    //         $isListOfObjects = isset($value[0]) && is_array($value[0]);

    //         if ($isListOfObjects) {
    //             return collect($value)->map(function ($item) {
    //                 return collect($item)
    //                     ->map(fn($v, $k) => "$k: $v")
    //                     ->implode(', ');
    //             })->implode(' | ');
    //         }

    //         // ASSOCIATIVE (Week 1 style)
    //         return collect($value)->map(function ($inner, $key) {

    //             if (is_array($inner)) {
    //                 return $key . ' → ' . collect($inner)
    //                     ->map(fn($v, $k) => "$k: $v")
    //                     ->implode(', ');
    //             }

    //             return "$key: $inner";

    //         })->implode(' | ');
    //     }

    //     return (string) $value;
    // }
    public function getQuestionAnalysisData(Request $request): array
    {
        $sectionIds = $this->selectedFilterIds($request->input('sections', []));
        $subSectionIds = $this->selectedFilterIds($request->input('sub_sections', []));
        $questionIds = $this->selectedFilterIds($request->input('questions', []));

        $query = StakeholderReport::query()
            ->with([
                'stakeholder',
                'chapter',
                'zone',
                'field',
            ]);

        if ($request->filled('fields')) {
            $query->whereIn('field_id', $request->fields);
        }

        if ($request->filled('zones')) {
            $query->whereIn('zone_id', $request->zones);
        }

        if ($request->filled('chapters')) {
            $query->whereIn('chapter_id', $request->chapters);
        }

        $filterAnswers = function ($query) use ($sectionIds, $subSectionIds, $questionIds) {
            $query
                ->when($sectionIds, fn ($q) => $q->whereIn('question_section_id', $sectionIds))
                ->when($subSectionIds, fn ($q) => $q->whereIn('question_sub_section_id', $subSectionIds))
                ->when($questionIds, fn ($q) => $q->whereIn('question_id', $questionIds));
        };

        if ($sectionIds || $subSectionIds || $questionIds) {
            $query->whereHas('answers', $filterAnswers);
        }

        $query->with([
            'answers' => function ($query) use ($filterAnswers) {
                $filterAnswers($query);
                $query->with(['section', 'subSection', 'question']);
            },
        ]);

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $reports = $query->get();

        $rows = $reports->map(function ($report) {
            return [
                'year' => $report->year,
                'month' => $report->month,
                'chapter_id' => $report->chapter_id,
                'chapter_name' => $report->chapter->name ?? '-',
                'answers' => $report->answers->map(function ($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'question_label' => $answer->question->label ?? '-',
                        'question_order' => $answer->question->order ?? PHP_INT_MAX,
                        'value' => $this->decodeAnswer($answer->answer_value ?? $answer->answer),
                    ];
                })->all(),
            ];
        })->all();

        return $this->buildQuestionAnalysisSheets($rows);
    }

    /**
     * Build one worksheet per month where each row is a chapter and each column is a question item.
     */
    public function buildQuestionAnalysisSheets(array $reports): array
    {
        $monthOrder = [];
        $questionMeta = [];
        $chapterRows = [];

        foreach ($reports as $report) {
            $period = Carbon::create((int) $report['year'], (int) $report['month'], 1)->format('M Y');
            $monthOrder[$period] = Carbon::create((int) $report['year'], (int) $report['month'], 1)->timestamp;

            $chapterKey = (string) ($report['chapter_id'] ?? $report['chapter_name'] ?? '-');

            if (! isset($chapterRows[$period][$chapterKey])) {
                $chapterRows[$period][$chapterKey] = [
                    'Chapter' => $report['chapter_name'] ?? '-',
                ];
            }

            foreach ($report['answers'] ?? [] as $answer) {
                $questionId = (string) ($answer['question_id'] ?? '');
                $questionLabel = $answer['question_label'] ?? '-';

                if ($questionId === '') {
                    continue;
                }

                $questionMeta[$questionId] = [
                    'label' => $questionLabel,
                    'order' => $answer['question_order'] ?? PHP_INT_MAX,
                ];

                $chapterRows[$period][$chapterKey][$questionId][] = $answer['value'] ?? '-';
            }
        }

        if ($monthOrder === []) {
            return [
                'No Data' => [
                    'headers' => ['Chapter'],
                    'rows' => [],
                ],
            ];
        }

        uasort($monthOrder, static fn ($left, $right) => $left <=> $right);
        uasort($questionMeta, static function (array $left, array $right) {
            $orderCompare = ($left['order'] ?? PHP_INT_MAX) <=> ($right['order'] ?? PHP_INT_MAX);

            return $orderCompare !== 0
                ? $orderCompare
                : strcmp($left['label'] ?? '', $right['label'] ?? '');
        });

        $questionHeaders = array_map(static fn (array $meta) => $meta['label'] ?? '-', $questionMeta);
        $sheets = [];

        foreach (array_keys($monthOrder) as $period) {
            $rows = [];

            foreach ($chapterRows[$period] ?? [] as $row) {
                foreach (array_keys($questionMeta) as $questionId) {
                    $label = $questionMeta[$questionId]['label'] ?? '-';
                    $row[$label] = $this->normalizeAnswerValue($row[$questionId] ?? null);
                    unset($row[$questionId]);
                }

                $rows[] = $row;
            }

            usort($rows, static function (array $left, array $right) {
                return strcmp((string) ($left['Chapter'] ?? ''), (string) ($right['Chapter'] ?? ''));
            });

            $sheets[$period] = [
                'headers' => array_merge(['Chapter'], $questionHeaders),
                'rows' => $rows,
            ];
        }

        return $sheets;
    }

    /**
     * Backwards-compatible helper for the legacy "chapter/item/month columns" structure.
     * Converts it into the new month-sheet layout used by both the blade table and Excel export.
     */
    public function splitQuestionAnalysisByMonth(array $report): array
    {
        $headers = $report['headers'] ?? [];
        $rows = $report['rows'] ?? [];
        $months = array_values(array_filter($headers, static fn ($header) => ! in_array($header, ['Chapter', 'Item'], true)));

        if ($months === []) {
            return [
                'No Data' => [
                    'headers' => ['Chapter'],
                    'rows' => [],
                ],
            ];
        }

        $chapterQuestionMap = [];
        $questionOrder = [];

        foreach ($rows as $row) {
            $chapter = $row['Chapter'] ?? '-';
            $item = $row['Item'] ?? '-';

            $questionOrder[$item] = $questionOrder[$item] ?? count($questionOrder);

            foreach ($months as $month) {
                if (! array_key_exists($month, $row) || $row[$month] === null || $row[$month] === '') {
                    continue;
                }

                $chapterQuestionMap[$month][$chapter][$item][] = $row[$month];
            }
        }

        $orderedQuestions = array_keys($questionOrder);
        $sheets = [];

        foreach ($months as $month) {
            $sheetRows = [];

            foreach ($chapterQuestionMap[$month] ?? [] as $chapter => $questionValues) {
                $sheetRow = ['Chapter' => $chapter];

                foreach ($orderedQuestions as $item) {
                    $sheetRow[$item] = $this->normalizeAnswerValue($questionValues[$item] ?? null);
                }

                $sheetRows[] = $sheetRow;
            }

            $sheets[$month] = [
                'headers' => array_merge(['Chapter'], $orderedQuestions),
                'rows' => $sheetRows,
            ];
        }

        return $sheets;
    }

    private function selectedFilterIds($values): array
    {
        $values = (array) $values;

        if (in_array('all', $values, true)) {
            return [];
        }

        return array_values(array_filter($values, static fn ($value) => is_numeric($value)));
    }

    private function normalizeAnswerValue($value): string
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        if (is_null($value)) {
            return '-';
        }

        if (is_string($value) || is_numeric($value)) {
            return (string) $value;
        }

        if (! is_array($value)) {
            return (string) $value;
        }

        // If list (multiple answers)
        if (array_is_list($value)) {

            return implode(' | ', array_map(function ($item) {

                if (is_array($item)) {
                    return collect($item)->map(function ($v, $k) {
                        if (is_array($v)) {
                            return $k.': '.collect($v)->map(function ($vv, $kk) {

                                if (is_array($vv)) {
                                    return $kk.': '.json_encode($vv);
                                }

                                return "$kk: $vv";

                            })->implode(', ');
                        }

                        return "$k: $v";

                    })->implode(' | ');
                }

                return (string) $item;

            }, $value));
        }

        // Associative / nested structure
        return collect($value)->map(function ($inner, $key) {

            if (is_array($inner)) {
                return $key.' → '.collect($inner)
                    ->map(fn ($v, $k) => "$k: $v")
                    ->implode(', ');
            }

            return "$key: $inner";

        })->implode(' | ');
    }

    private function decodeAnswer($value)
    {
        if (is_string($value)) {

            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return $value;
    }
}
