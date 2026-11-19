<?php

namespace App\Http\Controllers;

use App\Services\ExcelService;
use App\Services\GrowthAnalysisService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrowthAnalysisController extends Controller
{
    public function admin(Request $request, GrowthAnalysisService $service)
    {
        return $this->respond($request, $service, true);
    }

    public function stakeholder(Request $request, GrowthAnalysisService $service)
    {
        return $this->respond($request, $service, false);
    }

    private function respond(Request $request, GrowthAnalysisService $service, bool $isAdmin)
    {
        $user = $isAdmin ? Auth::user() : Auth::guard('stakeholder')->user();
        $scope = app(ReportService::class)->getScopedEntitiesForUser($user, $isAdmin);
        $measures = $service->measures();
        if ($request->query('format') === 'json' || $request->query('format') === 'excel') {
            $result = $service->build($request, $scope, $measures);
            if ($request->query('format') === 'json') return response()->json($result);

            $unit = $result['period'] === 'year' ? 'year' : 'month';
            $previousHeader = 'Previous '.$unit;
            $latestHeader = 'Latest '.$unit;
            $coverageHeader = ucfirst($unit).'s reported';
            $summaryHeaders = ['Entity', 'Measure', 'Calculation', $previousHeader, 'Previous value', $latestHeader, 'Latest value', 'Change', 'Change %', $coverageHeader, 'Status'];
            $summaryRows = array_map(fn ($row) => [
                'Entity' => $row['name'], 'Measure' => $result['measure']['label'], 'Calculation' => $result['aggregation'],
                $previousHeader => $row['previous_month'], 'Previous value' => $row['previous'],
                $latestHeader => $row['latest_month'], 'Latest value' => $row['latest'],
                'Change' => $row['change'], 'Change %' => $row['percent'],
                $coverageHeader => $row['reported_months'].' / '.count($result['months']),
                'Status' => ucfirst($row['status']),
            ], $result['series']);
            $monthlyHeaders = array_merge(['Entity', 'Measure', 'Calculation'], $result['months']);
            $monthlyRows = array_map(function ($row) use ($result) {
                $record = ['Entity' => $row['name'], 'Measure' => $result['measure']['label'], 'Calculation' => $result['aggregation']];
                foreach ($result['months'] as $index => $month) $record[$month] = $row['values'][$index];
                return $record;
            }, $result['series']);
            $contributorHeaders = array_merge(['Entity', 'Measure'], $result['months']);
            $contributorRows = array_map(function ($row) use ($result) {
                $record = ['Entity' => $row['name'], 'Measure' => $result['measure']['label']];
                foreach ($result['months'] as $index => $month) $record[$month] = $row['contributors'][$index];
                return $record;
            }, $result['series']);
            $aspectHeaders = ['Entity', 'Aspect', 'Type', 'Calculation', $previousHeader, 'Previous value', $latestHeader, 'Latest value', 'Change', 'Change %', $coverageHeader, 'Status'];
            $aspectRows = array_map(fn ($item) => [
                'Entity' => $result['focus_name'], 'Aspect' => $item['name'], 'Type' => $item['kind'], 'Calculation' => $result['aggregation'],
                $previousHeader => $item['previous_month'], 'Previous value' => $item['previous'],
                $latestHeader => $item['latest_month'], 'Latest value' => $item['latest'],
                'Change' => $item['change'], 'Change %' => $item['percent'],
                $coverageHeader => $item['reported_months'].' / '.count($result['months']),
                'Status' => ucfirst($item['status']),
            ], $result['aspects']);
            return ExcelService::downloadMultipleSheetsWithHeaders([
                'Growth summary' => ['headers' => $summaryHeaders, 'rows' => $summaryRows],
                ucfirst($unit).'ly values' => ['headers' => $monthlyHeaders, 'rows' => $monthlyRows],
                ($result['period'] === 'year' ? 'Reporting months' : 'Contributing campuses') => ['headers' => $contributorHeaders, 'rows' => $contributorRows],
                'Aspects' => ['headers' => $aspectHeaders, 'rows' => $aspectRows],
            ], 'growth_analysis.xlsx');
        }

        return view('admin.reports.analytics.growth', [
            'isAdmin' => $isAdmin,
            'user' => $user,
            'fields' => $scope['fields'],
            'zones' => $scope['zones'],
            'measures' => $measures,
        ]);
    }
}
