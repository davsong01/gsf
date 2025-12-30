<?php

namespace App\Services;

use Carbon\Carbon;


class ReportAnalyticsService
{
    public function reportGraphStats($request, string $period)
    {
        $table = 'transaction_all';

        // Determine date selection based on period (always DATE, no time)
        $dateSelect = match ($period) {
            'daily'   => "DATE($table.created_at)",
            'weekly'  => "DATE(DATE_SUB($table.created_at, INTERVAL WEEKDAY($table.created_at) DAY))", // Monday of the week
            'monthly' => "DATE(DATE_FORMAT($table.created_at, '%Y-%m-01'))", // First day of the month
            'yearly'  => "DATE(DATE_FORMAT($table.created_at, '%Y-01-01'))", // First day of the year
            default   => "DATE($table.created_at)",
        };

        $q = DB::table($table)
            ->whereIn("$table.status", self::successfulStatuses());

        $q = $this->applyDateRangeFilter($q, $table, $period, $request, 'created_at');

        $q->selectRaw("
            $table.product_id,
            COALESCE($table.product_name, 'Unknown') AS product_name,
            $dateSelect AS graph_date,
            SUM($table.amount) AS total_amount,
            COUNT(*) AS total_count
        ");

        // Group by product and period
        $groupBy = ['product_id', 'product_name', DB::raw($dateSelect)];

        $q->groupBy($groupBy)
            ->orderBy('graph_date');

        // Domain filters
        if ($request->filled('domain_urlx')) {
            $domainId = Domain::where('url', $request->domain_urlx)->value('id');
            if ($domainId) $q->where("$table.domain_id", $domainId);
        } elseif ($request->filled('domain_id')) {
            $q->where("$table.domain_id", $request->domain_id);
        }

        $adminRoles     = session('admin_role_user', []);
        $currentAdminId = session('admin_id');

        if (array_intersect($adminRoles, [15, 26, 43, 44])) {
            $request->merge(['business_developer_id' => $currentAdminId]);
        }

        if ($request->business_developer_id || $request->category_id) {
            $q->join('customers', 'customers.id', '=', "$table.customer_id");
        }

        $q->when($request->business_developer_id, fn($x) => $x->where('customers.account_manager_id', $request->business_developer_id));
        $q->when($request->category_id, fn($x) => $x->where('customers.category_id', $request->category_id));

        $directFilters = [
            'customer' => 'customer_id',
            'channel'  => 'channel',
            'email'    => 'email',
            'phone'    => 'phone',
            'platform' => 'platform',
        ];

        foreach ($directFilters as $req => $col) {
            $q->when($request->$req, fn($x) => $x->where("$table.$col", $request->$req));
        }

        if (!empty($request->legends)) {
            $q->whereIn("$table.product_id", $request->legends);
        }

        $records = $q->get()->groupBy('product_id');

        // Build labels and datasets
        $labels = $records
            ->flatten()
            ->pluck('graph_date')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $productIds    = $records->keys()->toArray();
        $productColors = $this->getLegendColors($productIds);
        $datasets      = [];

        foreach ($records as $productId => $rows) {
            $line   = [];
            $counts = [];
            foreach ($labels as $date) {
                $match = $rows->firstWhere('graph_date', $date);
                $line[]   = $match ? (float) $match->total_amount : 0;
                $counts[] = $match ? (int) $match->total_count : 0;
            }
            $color = $productColors[$productId] ?? '#999';
            $datasets[] = [
                'label'           => $rows->first()->product_name,
                'product_id'      => $productId,
                'data'            => $line,
                'total_count'     => $counts,
                'borderColor'     => $color,
                'backgroundColor' => $color,
                'tension'         => 0.4,
            ];
        }

        return [
            'labels'           => $labels,
            'datasets'         => $datasets,
            'products'         => Product::select('id', 'name')->whereIn('id', $productIds)->get(),
            'admins'           => getAdminsByRoles([15, 26, 43, 44]),
            'roles'            => Role::where('type', 'customer')->pluck('name', 'id')->toArray(),
            'admin_roles'      => $adminRoles,
            'current_admin_id' => $currentAdminId,
            'period'           => $period,
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

    function getLegendColors(array $ids, $cacheKey = 'product_colors'): array
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