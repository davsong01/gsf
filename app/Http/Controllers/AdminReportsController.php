<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\StakeholderReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ReportAnalyticsService;


class AdminReportsController extends Controller
{
    private $reportAnalyticService;

    public function __construct(){
        $this->reportAnalyticService = new ReportAnalyticsService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $user->role_id = $user->role;

        $isAdmin = true;
        $data = app(ReportService::class)
            ->index($request, $user, $isAdmin);


        return view('admin.reports.index', array_merge($data, compact('user', 'isAdmin')));
    }

    public function create()
    {
    }

    public function fixOrphanReport()
    {
        $count = 0;

        StakeholderReport::with([
            'stakeholder:id,chapter_id,zone_id,field_id',
            'chapter:id,zone_id,field_id'
        ])
        ->where(function ($q) {
            $q->whereNull('zone_id')
            ->orWhereNull('field_id')
            ->orWhereNull('chapter_id');
        })
        ->chunk(200, function ($reports) use (&$count) {

            foreach ($reports as $report) {

                $chapterId = $report->stakeholder?->chapter_id;
                $zoneId    = $report->stakeholder?->zone_id ?? $report->chapter?->zone_id;
                $fieldId   = $report->stakeholder?->field_id ?? $report->chapter?->field_id;

                $data = [];

                if (is_null($report->chapter_id) && $chapterId) {
                    $data['chapter_id'] = $chapterId;
                }

                if (is_null($report->zone_id) && $zoneId) {
                    $data['zone_id'] = $zoneId;
                }

                if (is_null($report->field_id) && $fieldId) {
                    $data['field_id'] = $fieldId;
                }

                if (!empty($data)) {
                    $report->update($data);
                    $count++;
                }
            }

        });

        return back()->with('message', "{$count} reports fixed");
    }

    public function update(Request $request, StakeholderReport $stakeholderreport)
    {
        $stakeholder = auth()->user();

        $validated = app(ReportService::class)->validateRequest($request);
        $isAdmin = true;

        $result = app(ReportService::class)
            ->saveReport($stakeholder, $stakeholderreport, $validated, $isAdmin);

        if (!$result['status']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('stakeholderreports.index')
            ->with('message', $result['message']);
    }

    public function show(StakeholderReport $stakeholderreport)
    {
        $data = app(ReportService::class)->prepareViewData($stakeholderreport, true);

        return view('reports.pdf_template', [
            'isAdmin'     => true,
            'report'     => $stakeholderreport,
            'sections'   => $data['sections']
        ]);
    }

   public function edit(StakeholderReport $stakeholderreport)
    {
        $user = auth()->user();
        $isAdmin = true;
        return view(
            'admin.reports.create',
            app(ReportService::class)->prepareEditData($stakeholderreport, $user, true)
            + compact('user', 'isAdmin')
        );
    }


    public function destroy(StakeholderReport $stakeholderreport)
    {
        app(ReportService::class)->deleteReport($stakeholderreport);

        return redirect()->route('stakeholderreports.index')
            ->with('message', 'Report deleted successfully');
    }

    public function nudge(StakeholderReport $report)
    {
        $stakeholder = Auth::user();

        app(ReportService::class)->nudgeReportActors($stakeholder, $report);

        return back()->with('message', 'Report actors have been nudged successfully!');
    }

    public function reportAnalyticsIndex(){
        $isAdmin = true;
        $data = [];

        return view('admin.reports.analytics.index', array_merge($data, compact('isAdmin')));
    }

    public function reportAnalyticsType(Request $request, $type){
        $data = [
            'isAdmin' => true,
            'collapse' => filter_var($request->get('collapse_data'), FILTER_VALIDATE_BOOL),
            'legendName' => 'Compliance Trend',
            'graphType' => $request->collapse ? 'single' : 'multi',
            'allowProductCollapse' => true,
            'legends' => Chapter::orderBy('name')->get(),
            'allowProductCollapse' => true,
            'level' => $request->level ?? 'chapter',
            'type' => $request->type ?? 'chapter',
        ];

        if ($request->ajax()) {
            if($request->type == 'compliance'){
                $result = $this->reportAnalyticService->fetchAnalyticsTypeData($request);
            }
            // dd($result['labels'] );
            // $result['labels'] = ["2026-01", "2026-02"];

            // $result['datasets'] = [
            //     [
            //         'product_id' => 1,
            //         'label' => 'Reports Submitted',
            //         'data' => [12, 19, 8, 15, 22, 18],
            //         'borderColor' => '#36A2EB',
            //         'backgroundColor' => '#36A2EB',
            //         'borderWidth' => 2,
            //         'fill' => false,
            //         'tension' => 0.4
            //     ],
            //     [
            //         'product_id' => 2,
            //         'label' => 'Approved Reports',
            //         'data' => [10, 14, 6, 12, 18, 16],
            //         'borderColor' => '#FF6384',
            //         'backgroundColor' => '#FF6384',
            //         'borderWidth' => 2,
            //         'fill' => false,
            //         'tension' => 0.4
            //     ]
            // ];

            return response()->json([
                'labels'     => $result['labels'],
                'datasets'   => $result['datasets'],
                'graph_type' => $data['graphType'],
            ]);
        }



        return view('admin.reports.analytics.compliance', array_merge($data));
    }

}
