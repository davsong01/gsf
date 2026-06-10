<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\Field;
use App\Models\StakeholderReport;
use App\Models\Zone;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
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
        $availableZoneIds  = $scope['zoneIds']->toArray() ?? [];

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
                ->when($fieldFilter, fn($q) => $q->whereIn('field_id', $fieldFilter))
                ->when($zoneFilter, fn($q) => $q->whereIn('zone_id', $zoneFilter))
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
                        if ($report->national_rejected_at) $status = 7;
                        elseif ($report->national_status) $status = 8;
                        elseif ($report->field_rejected_at) $status = 5;
                        elseif ($report->field_status) $status = 6;
                        elseif ($report->zone_rejected_at) $status = 3;
                        elseif ($report->zone_status) $status = 4;
                        elseif ($report->edit_mode) $status = 1;
                        else $status = 2;
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
                    ->when($fieldFilter, fn($q) => $q->whereIn('id', $fieldFilter))
                    ->orderBy('name')
                    ->get();
            } else {
                $groups = Zone::query()
                    ->when($zoneFilter, fn($q) => $q->whereIn('id', $zoneFilter))
                    ->when($fieldFilter, fn($q) => $q->whereIn('field_id', $fieldFilter))
                    ->orderBy('name')
                    ->get();
            }

            foreach ($groups as $group) {

                $chaptersInGroup = Chapter::query()
                    ->when($groupBy === 'field', fn($q) => $q->where('field_id', $group->id))
                    ->when($groupBy === 'zone', fn($q) => $q->where('zone_id', $group->id))
                    ->when($fieldFilter, fn($q) => $q->whereIn('field_id', $fieldFilter))
                    ->when($zoneFilter, fn($q) => $q->whereIn('zone_id', $zoneFilter))
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
                            if ($report->national_rejected_at) $status = 7;
                            elseif ($report->national_status) $status = 8;
                            elseif ($report->field_rejected_at) $status = 5;
                            elseif ($report->field_status) $status = 6;
                            elseif ($report->zone_rejected_at) $status = 3;
                            elseif ($report->zone_status) $status = 4;
                            elseif ($report->edit_mode) $status = 1;
                            else $status = 2;
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
            ->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))
            ->toArray();

        return [
            'labels' => $labels,
            'datasets' => $datasets,
            'status_levels' => $statusLevels,
        ];
    }

    public function generateSubmissionStatusReport($scope, $from = null, $to = null): array
    {
        $query = DB::table('stakeholder_reports');

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

        $chapters = DB::table('chapters')->get()->keyBy('id');
        $fields   = DB::table('fields')->get()->keyBy('id');

        // =========================
        // BUILD OUTPUT
        // =========================
        return [
            // -------------------------
            // NATIONAL
            // -------------------------
            'nationallyApproved' => $this->group($reports, $chapters, $fields, function ($r) {
                return $r->national_status == 1;
            }),

            'nationalDeclined' => $this->group($reports, $chapters, $fields, function ($r) {
                return $r->national_status == 2;
            }),

            'nationalPending' => $this->group($reports, $chapters, $fields, function ($r) {
                return $r->national_status == 0;
            }),

            // -------------------------
            // ZONE
            // -------------------------
            'zoneApproved' => $this->group($reports, $chapters, $fields, function ($r) {
                return $r->zone_status == 1;
            }),

            'pendingZoneApproval' => $this->group($reports, $chapters, $fields, function ($r) {
                return in_array($r->zone_status, [0, 2]);
            }),

            'zoneDeclined' => $this->group($reports, $chapters, $fields, function ($r) {
                return $r->zone_status == 2;
            }),

            // -------------------------
            // FIELD
            // -------------------------
            'fieldApproved' => $this->group($reports, $chapters, $fields, function ($r) {
                return $r->field_status == 1;
            }),

            'pendingFieldApproval' => $this->group($reports, $chapters, $fields, function ($r) {
                return in_array($r->field_status, [0, 2]) && $r->zone_status == 1;
            }),

            'fieldDeclined' => $this->group($reports, $chapters, $fields, function ($r) {
                return $r->field_status == 2;
            }),

            // -------------------------
            // COMPLETENESS
            // -------------------------
            'monthsYetToSubmit' => $this->getMonthsYetToSubmit(
                $reports,
                $chapters,
                $fields,
                $start,
                $end
            ),

            'neverSubmitted' => $this->getNeverSubmitted($reports, $chapters, $fields),
        ];
    }

    protected function getNeverSubmitted($reports, $chapters, $fields): array
    {
        $submitted = [];

        foreach ($reports as $r) {
            $submitted[$r->chapter_id] = true;
        }

        $result = [];

        foreach ($chapters as $chapter) {

            if (isset($submitted[$chapter->id])) {
                continue;
            }

            $field = $fields[$chapter->field_id] ?? null;
            if (!$field) continue;

            $result[$field->name][] = $chapter->name;
        }

        return $result;
    }

    protected function group($reports, $chapters, $fields, callable $filter): array
    {
        $result = [];

        foreach ($reports as $r) {

            if (!$filter($r)) continue;

            $month = $this->normalizeMonth($r->month);
            if (!$month) continue;

            $chapter = $chapters[$r->chapter_id] ?? null;
            $field   = $fields[$r->field_id] ?? null;

            if (!$chapter || !$field) continue;

            $result[$field->name][$chapter->name][] = $month;
        }

        return $this->uniqueMonths($result);
    }
    
    protected function normalizeMonth($value): ?string
    {
        if (!$value) return null;

        if (is_numeric($value)) {
            return Carbon::create()->month((int)$value)->format('F');
        }

        return Carbon::parse($value)->format('F');
    }

    protected function getMonthsYetToSubmit($reports, $chapters, $fields, $from, $to): array
    {
        $expected = $this->getMonthRange($from, $to);

        $submitted = [];

        foreach ($reports as $r) {
            $month = $this->normalizeMonth($r->month);
            if (!$month) continue;

            $submitted[$r->chapter_id][] = $month;
        }

        $result = [];

        foreach ($chapters as $chapter) {

            $field = $fields[$chapter->field_id] ?? null;
            if (!$field) continue;

            $done = $submitted[$chapter->id] ?? [];

            $missing = array_values(array_diff($expected, $done));

            if ($missing) {
                $result[$field->name][$chapter->name] = $missing;
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

        $months = [];

        while ($start->lte($end)) {
            $months[] = $start->format('F Y');
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
                'to'   => $request->to_date,

                // MAIN DATA CONTRACT
                'nationallyApproved'     => $data['nationallyApproved'] ?? [],
                'nationalDeclined'       => $data['nationalDeclined'] ?? [],
                'nationalPending'        => $data['nationalPending'] ?? [],

                'zoneApproved'           => $data['zoneApproved'] ?? [],
                'pendingZoneApproval'    => $data['pendingZoneApproval'] ?? [],
                'zoneDeclined'           => $data['zoneDeclined'] ?? [],

                'fieldApproved'          => $data['fieldApproved'] ?? [],
                'pendingFieldApproval'   => $data['pendingFieldApproval'] ?? [],
                'fieldDeclined'          => $data['fieldDeclined'] ?? [],

                'monthsYetToSubmit'      => $data['monthsYetToSubmit'] ?? [],
                'neverSubmitted'      => $data['neverSubmitted'] ?? [],
            ]
        )
        ->setPaper('a4', 'landscape');

        return $pdf->download('gsf_monthly_report.pdf');
    }
}
