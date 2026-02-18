<?php

namespace App\Services;

use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Models\StakeholderReport;
use Illuminate\Support\Facades\DB;


class ReportAnalyticsService
{

    // public function fetchAnalyticsTypeData(Request $request, $scope = [])
    // {
    //     $groupBy = $request->group_by ?? 'chapter'; // default group

    //     $statusLevels = [
    //         0 => 'Not Submitted',
    //         1 => 'Currently Editing',
    //         2 => 'Submitted',
    //         3 => 'Zone Rejected',
    //         4 => 'Zone Approved',
    //         5 => 'Field Rejected',
    //         6 => 'Field Approved',
    //         7 => 'National Rejected',
    //         8 => 'National Approved',
    //     ];

    //     $from = $request->from_date
    //         ? Carbon::parse($request->from_date)->startOfMonth()
    //         : Carbon::now()->startOfMonth();

    //     $to = $request->to_date
    //         ? Carbon::parse($request->to_date)->endOfMonth()
    //         : Carbon::now()->endOfMonth();

    //     $months = [];
    //     $cursor = $from->copy();
    //     while ($cursor <= $to) {
    //         $months[] = $cursor->format('Y-m');
    //         $cursor->addMonth();
    //     }

    //     $datasets = [];
    //     $availableFields = $scope['fieldIds'];
    //     $availableZones = $scope['zoneIds'];

    //     if ($groupBy === 'chapter') {
    //         $chapters = Chapter::query()
    //             ->when($request->filled('fields'), fn($q) => $q->whereIn('field_id', $request->fields))
    //             ->when($request->filled('zones'), fn($q) => $q->whereIn('zone_id', $request->zones))
    //             ->orderBy('name')
    //             ->get();

    //         foreach ($chapters as $chapter) {
    //             $data = [];
    //             $tooltipLabels = [];

    //             foreach ($months as $month) {
    //                 [$year, $m] = explode('-', $month);

    //                 $report = StakeholderReport::where('chapter_id', $chapter->id)
    //                     ->where('year', $year)
    //                     ->where('month', $m)
    //                     ->first();

    //                 $status = 0;
    //                 if ($report) {
    //                     if ($report->national_rejected_at) $status = 7;
    //                     elseif ($report->national_status) $status = 8;
    //                     elseif ($report->field_rejected_at) $status = 5;
    //                     elseif ($report->field_status) $status = 6;
    //                     elseif ($report->zone_rejected_at) $status = 3;
    //                     elseif ($report->zone_status) $status = 4;
    //                     elseif ($report->edit_mode) $status = 1;
    //                     else $status = 2;
    //                 }

    //                 $data[] = $status;
    //                 $tooltipLabels[] = [
    //                     ['chapter_name' => $chapter->name, 'status' => $status, 'status_label' => $statusLevels[$status]]
    //                 ];
    //             }

    //             $datasets[] = [
    //                 'legend_id' => $chapter->id,
    //                 'label' => $chapter->name,
    //                 'data' => $data,
    //                 'tooltip' => $tooltipLabels,
    //                 'borderWidth' => 1.5,
    //                 'tension' => 0.3,
    //                 'fill' => false,
    //             ];
    //         }
    //     } else {
    //         // group by field or zone
    //         if ($groupBy === 'field') {
    //             $groups = Field::query()
    //                 ->when($request->filled('fields'), fn($q) => $q->whereIn('id', $request->fields))
    //                 ->get();
    //         } else { // zone
    //             $groups = Zone::query()
    //                 ->when($request->filled('fields'), fn($q) => $q->whereIn('field_id', $request->fields)) // zone depends on fields
    //                 ->when($request->filled('zones'), fn($q) => $q->whereIn('id', $request->zones))
    //                 ->get();
    //         }

    //         foreach ($groups as $group) {
    //             // Chapters in this group, filtered by dependent selections
    //             $chaptersInGroup = Chapter::query()
    //                 ->when($groupBy === 'field', fn($q) => $q->where('field_id', $group->id))
    //                 ->when($groupBy === 'zone', fn($q) => $q->where('zone_id', $group->id))
    //                 ->when($request->filled('fields'), fn($q) => $q->whereIn('field_id', $request->fields))
    //                 ->when($request->filled('zones'), fn($q) => $q->whereIn('zone_id', $request->zones))
    //                 ->get();

    //             $data = [];
    //             $tooltipLabels = [];

    //             foreach ($months as $month) {
    //                 [$year, $m] = explode('-', $month);

    //                 $maxStatus = 0;
    //                 $chapterStatuses = [];

    //                 foreach ($chaptersInGroup as $chapter) {
    //                     $report = StakeholderReport::where('chapter_id', $chapter->id)
    //                         ->where('year', $year)
    //                         ->where('month', $m)
    //                         ->first();

    //                     $status = 0;
    //                     if ($report) {
    //                         if ($report->national_rejected_at) $status = 7;
    //                         elseif ($report->national_status) $status = 8;
    //                         elseif ($report->field_rejected_at) $status = 5;
    //                         elseif ($report->field_status) $status = 6;
    //                         elseif ($report->zone_rejected_at) $status = 3;
    //                         elseif ($report->zone_status) $status = 4;
    //                         elseif ($report->edit_mode) $status = 1;
    //                         else $status = 2;
    //                     }

    //                     $chapterStatuses[] = [
    //                         'chapter_name' => $chapter->name,
    //                         'status' => $status,
    //                         'status_label' => $statusLevels[$status]
    //                     ];

    //                     if ($status > $maxStatus) $maxStatus = $status;
    //                 }

    //                 $data[] = $maxStatus;
    //                 $tooltipLabels[] = $chapterStatuses;
    //             }

    //             $datasets[] = [
    //                 'legend_id' => $group->id,
    //                 'label' => $group->name,
    //                 'data' => $data,
    //                 'tooltip' => $tooltipLabels,
    //                 'borderWidth' => 1.5,
    //                 'tension' => 0.3,
    //                 'fill' => false,
    //             ];
    //         }
    //     }

    //     $labels = collect($months)->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->toArray();

    //     return [
    //         'labels' => $labels,
    //         'datasets' => $datasets,
    //         'status_levels' => $statusLevels,
    //     ];
    // }
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

    /** ======================
     * GROUP BY CHAPTER
     ====================== */
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

}
