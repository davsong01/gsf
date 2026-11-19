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

            $summaryHeaders = ['Entity', 'Measure', 'Calculation', 'First reported', 'Latest reported', 'Change', 'Change %', 'Months reported', 'Status'];
            $summaryRows = array_map(fn ($row) => [
                'Entity' => $row['name'], 'Measure' => $result['measure']['label'], 'Calculation' => $result['aggregation'],
                'First reported' => $row['first'], 'Latest reported' => $row['latest'],
                'Change' => $row['change'], 'Change %' => $row['percent'],
                'Months reported' => $row['reported_months'].' / '.count($result['months']),
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
            $aspectHeaders = ['Entity', 'Aspect', 'Type', 'Calculation', 'First reported', 'Latest reported', 'Change', 'Change %', 'Months reported', 'Status'];
            $aspectRows = array_map(fn ($item) => [
                'Entity' => $result['focus_name'], 'Aspect' => $item['name'], 'Type' => $item['kind'], 'Calculation' => $result['aggregation'],
                'First reported' => $item['first'], 'Latest reported' => $item['latest'],
                'Change' => $item['change'], 'Change %' => $item['percent'],
                'Months reported' => $item['reported_months'].' / '.count($result['months']),
                'Status' => ucfirst($item['status']),
            ], $result['aspects']);
            return ExcelService::downloadMultipleSheetsWithHeaders([
                'Growth summary' => ['headers' => $summaryHeaders, 'rows' => $summaryRows],
                'Monthly values' => ['headers' => $monthlyHeaders, 'rows' => $monthlyRows],
                'Contributing campuses' => ['headers' => $contributorHeaders, 'rows' => $contributorRows],
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
