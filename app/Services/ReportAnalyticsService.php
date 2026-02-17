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


    public function fetchAnalyticsTypeData(Request $request)
    {
        // Status hierarchy
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

        // Determine date range
        $from = $request->from_date
            ? Carbon::parse($request->from_date)->startOfMonth()
            : Carbon::now()->startOfMonth();

        $to = $request->to_date
            ? Carbon::parse($request->to_date)->endOfMonth()
            : Carbon::now()->endOfMonth();

        // Build months array
        $months = [];
        $cursor = $from->copy();
        while ($cursor <= $to) {
            $months[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        // Fetch chapters (optional filters)
        $chaptersQuery = Chapter::query();
        if ($request->filled('zones')) {
            $chaptersQuery->whereIn('zone_id', $request->zones);
        }
        if ($request->filled('fields')) {
            $chaptersQuery->whereIn('field_id', $request->fields);
        }
        $chapters = $chaptersQuery->orderBy('name')->get();

        $filterStatus = $request->submission_status; // null or 0-8
        $datasets = [];

        foreach ($chapters as $chapter) {
            $data = [];
            $tooltipLabels = [];
            $chapterMatchesFilter = false;

            foreach ($months as $month) {
                [$year, $m] = explode('-', $month);

                $report = StakeholderReport::where('chapter_id', $chapter->id)
                    ->where('year', $year)
                    ->where('month', $m)
                    ->first();

                // Determine status
                if (!$report) {
                    $status = 0;
                } else {
                    if ($report->national_rejected_at) { $status = 7; }
                    elseif ($report->national_status) { $status = 8; }
                    elseif ($report->field_rejected_at) { $status = 5; }
                    elseif ($report->field_status) { $status = 6; }
                    elseif ($report->zone_rejected_at) { $status = 3; }
                    elseif ($report->zone_status) { $status = 4; }
                    elseif ($report->edit_mode) { $status = 1; }
                    else { $status = 2; }
                }

                // Apply filter: if filter is set, only keep chapters with at least one month matching
                if (is_null($filterStatus) || $filterStatus == $status) {
                    $chapterMatchesFilter = true;
                }

                $data[] = $status;
                $tooltipLabels[] = $statusLevels[$status];
            }

            if (is_null($filterStatus) || $chapterMatchesFilter) {
                $datasets[] = [
                    'legend_id'   => $chapter->id,
                    'label'       => $chapter->name,
                    'data'        => $data,
                    'tooltip'     => $tooltipLabels,
                    'borderWidth' => 1.5,
                    'tension'     => 0.3,
                    'fill'        => false,
                ];
            }
        }

        $labels = collect($months)->map(fn($m) =>
            Carbon::createFromFormat('Y-m', $m)->format('M Y')
        )->toArray();

        return [
            'labels'        => $labels,
            'datasets'      => $datasets,
            'status_levels' => $statusLevels,
        ];
    }


}
