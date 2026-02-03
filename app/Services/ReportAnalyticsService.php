<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportAnalyticsService
{
  public function fetchAnalyticsTypeData(Request $request)
{
    $levelColumn = match ($request->level) {
        'chapter' => 'chapter_id',
        'zone'    => 'zone_id',
        'field'   => 'field_id',
        default   => 'chapter_id',
    };

    // Get all legends for this level
    $legends = DB::table(
        $levelColumn === 'chapter_id' ? 'chapters' :
        ($levelColumn === 'zone_id' ? 'zones' : 'fields')
    )
    ->select('id', 'name')
    ->get()
    ->keyBy('id');

    $query = DB::table('stakeholder_reports');

    if ($request->filled('legends')) {
        $query->whereIn($levelColumn, $request->legends);
    }

    if ($request->filled('from_date')) {
        $from = \Carbon\Carbon::parse($request->from_date)->format('Y-m');
        $query->whereRaw("CONCAT(year,'-',LPAD(month,2,'0')) = ?", [$from]);
    }

    $records = $query
        ->select(
            "$levelColumn as legend_id",
            DB::raw("CONCAT(year,'-',LPAD(month,2,'0')) as graph_date"),
            DB::raw("COUNT(*) as submitted")
        )
        ->groupBy('legend_id', 'graph_date')
        ->orderBy('graph_date')
        ->get()
        ->groupBy('legend_id');

    // Collect all months
    $allMonths = $records->flatten()->pluck('graph_date')->unique()->sort()->values();

    $labels = $allMonths->map(function ($month) {
        return \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M, Y');
    })->toArray();

    $datasets = [];

    foreach ($records as $legendId => $rows) {
        $legendName = $legends[$legendId]->name ?? 'Unknown';

        $data = [];
        foreach ($allMonths as $month) {
            $row = $rows->firstWhere('graph_date', $month);
            $data[] = $row->submitted ?? 0;
        }

        $datasets[] = [
            'label'     => $legendName,
            'legend_id' => $legendId,
            'data'      => $data,
        ];
    }

    return [
        'labels'     => $labels,
        'datasets'   => $datasets,
        'graph_type' => 'multi',
    ];
}



    public function applyDateRangeFilter($q, $table, $period, $request, $start_date_column = 'start_date')
    {
        $now = Carbon::now();

        $col = "$table.$start_date_column";
        if ($period === 'daily') {
            $from = $request->date_from
                ? Carbon::parse($request->date_from)->startOfDay()
                : $now->copy()->startOfDay();

            $to = $request->date_to
                ? Carbon::parse($request->date_to)->endOfDay()
                : $now->copy()->endOfDay();

            $q->whereBetween($col, [$from, $to]);
        } elseif ($period === 'weekly') {
            $from = $request->date_from
                ? Carbon::parse($request->date_from)->startOfWeek()->startOfDay()
                : $now->copy()->startOfWeek()->startOfDay();

            $to = $request->date_to
                ? Carbon::parse($request->date_to)->endOfDay()
                : $now->copy()->endOfWeek()->endOfDay();

            $q->whereBetween($col, [$from, $to]);
        } elseif ($period === 'monthly') {
            $start = $request->date_from
                ? Carbon::parse($request->date_from)->startOfDay()
                : $now->copy()->startOfMonth()->startOfDay();

            $end = $request->date_to
                ? Carbon::parse($request->date_to)->endOfDay()
                : $now->copy()->endOfMonth()->endOfDay();

            $q->whereBetween($col, [$start, $end]);
        } elseif ($period === 'yearly') {
            $from = $request->date_from
                ? Carbon::createFromFormat(strlen($request->date_from) === 4 ? 'Y' : 'Y-m-d', $request->date_from)->startOfYear()
                : $now->copy()->startOfYear()->startOfDay();

            $to = $request->date_to
                ? Carbon::createFromFormat(strlen($request->date_to) === 4 ? 'Y' : 'Y-m-d', $request->date_to)->endOfYear()
                : $now->copy()->endOfYear()->endOfDay();

            $q->whereBetween($col, [$from, $to]);
        }

        return $q;
    }

    public function getLegendColors(array $ids, $cacheKey = 'product_colors'): array
    {
        if (empty($ids)) {
            return [];
        }

        $cachedColors = cache()->get($cacheKey, []);

        $missingIds = array_diff($ids, array_keys($cachedColors));

        if (empty($missingIds)) {
            return $cachedColors;
        }

        $step = 360 / max(count($ids), 1);

        foreach ($ids as $i => $id) {

            if (!isset($cachedColors[$id])) {
                $hue = ($i * $step) % 360;
                $cachedColors[$id] = "hsl($hue, 70%, 55%)";
            }
        }

        // dd($cachedColors);
        cache()->put($cacheKey, $cachedColors, 3600);

        return $cachedColors;
    }
}
