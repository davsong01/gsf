<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Services\ReportService;
use App\Models\StakeholderReport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ReportAnalyticsService;
use App\Models\StakeholderQuestionSection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class StakeholderReportsController extends Controller
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
        $user = Auth::guard('stakeholder')->user();

        if ($user->role_id === 'Financial Secretary') {
            return redirect()->route('stakeholderpayment.index');
        }

        $isAdmin = false;

        $data = app(ReportService::class)
            ->index($request, $user, $isAdmin);

        return view('stakeholder.index', array_merge($data, compact('user','isAdmin')));
    }

    public function reportAnalyticsIndex(){
        $isAdmin = false;
        $data = [];

        return view('admin.reports.analytics.index', array_merge($data, compact('isAdmin')));
    }


    public function reportAnalyticsType(Request $request, $type)
    {
        $isAdmin = false;
        $user = auth()->guard('stakeholder')->user();
        $scope = app(ReportService::class)->getScopedEntitiesForUser($user, $isAdmin);

        // Fetch all available fields/zones for the dropdown
        $fields = DB::table('fields')->whereIn('id', $scope['fieldIds']->toArray())->orderBy('name')->get();
        $zones  = DB::table('zones')->whereIn('id', $scope['zoneIds']->toArray())->orderBy('name')->get();

        // Only use filters for querying, don't set defaults for the blade
        $selectedFields = $request->filled('fields') ? $request->fields : $scope['fieldIds']->toArray();
        $selectedZones  = $request->filled('zones')  ? $request->zones  : $scope['zoneIds']->toArray();

        // Fetch chapters based on actual selection
        $chaptersQuery = DB::table('chapters')->orderBy('name');

        if (!empty($selectedZones)) {
            $chaptersQuery->whereIn('zone_id', $selectedZones);
        } elseif (!empty($selectedFields)) {
            $chaptersQuery->whereIn('field_id', $selectedFields);
        }

        $chapters = $chaptersQuery->orderBy('name')->get();

        $data = [
            'isAdmin' => $isAdmin,
            'level'   => $request->level ?? 'chapter',
            'type'    => $type,
            'fields'  => $fields,
            'zones'   => $zones,
            'user'     => $user,
            'chapters'=> $chapters,
            'legends' => $chapters,
        ];

        $request['isAdmin'] = $isAdmin;
        // Handle Excel download
        if ($request->filter_type === 'excel') {
            $result = $this->reportAnalyticService->fetchAnalyticsTypeData($request, $scope);
            $labels = $result['labels'];
            $datasets = $result['datasets'];

            $exportData = [];
            foreach ($datasets as $chapterData) {
                $chapter = Chapter::with(['field', 'zone'])->find($chapterData['legend_id']);
                $row = [
                    'Chapter' => $chapterData['label'],
                    'Field'   => $chapter->field->name ?? '-',
                    'Zone'    => $chapter->zone->name ?? '-',
                ];
                foreach ($labels as $index => $month) {
                    $row[$month] = $chapterData['tooltip'][$index][0]['status_label'] ?? 'Not Submitted';
                }
                $exportData[] = $row;
            }

            $headers = array_merge(['Chapter', 'Field', 'Zone'], $labels);

            return ExcelService::download($exportData, $headers, 'chapter_compliance_report.xlsx');
        }

        if ($request->filter_type === 'pdf') {
            $reportService = app(ReportAnalyticsService::class);

            $data = $reportService->generateSubmissionStatusReport(
                $scope,
                $request->from_date ?? null,
                $request->to_date ?? null
            );

            return $reportService->downloadSubmissionStatusPdf(
                $data,
                $request
            );
        }

        // Handle AJAX chart request
        if ($request->isMethod('post')) {
            $result = $this->reportAnalyticService->fetchAnalyticsTypeData($request, $scope);

            return response()->json([
                'labels'        => $result['labels'],
                'datasets'      => $result['datasets'],
                'status_levels' => $result['status_levels'],
            ]);
        }

        // Normal GET view
        return view('admin.reports.analytics.compliance', $data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::guard('stakeholder')->user();

        $months = getMonths();
        
        $chapter = $user->chapter;

        $sections = StakeholderQuestionSection::isActive()
            ->with([
                'subsections' => function ($subQuery) {
                    $subQuery->isActive()->with([
                        'questions' => function ($q) {
                            $q->isActive()->orderBy('order');
                        }
                    ]);
                }
            ])
            ->orderBy('id')
            ->get();

        if(!in_array($user->role_id, chapterStakeholders())){
            return back()->with('error', 'Unauthorized Access');
        }

        $eligibleMonth = canAddReport($user->chapter_id);
        if(!($eligibleMonth['eligible'])) return redirect()->route('stakeholders.reports.index')->with('error', 'You cannot submit report for requested month.');

        $prefillData = [
            'chapter_name' => $chapter->name ?? '',
            'month' => $eligibleMonth['month_number'],
            'year' => $eligibleMonth['year'],
            'year_established' => $chapter->year_established ?? '',
            'session' => date('Y') - 1 . '/'. date('Y'),
            'president_name' => $chapter->chapterPresident->name ?? '',
        ];

        $isAdmin = false;
        return view('stakeholder.create', compact('months', 'sections', 'prefillData', 'user','isAdmin'));
    }

    public function edit(StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();
        $canEdit = app(ReportService::class)->canEditReport($report, $user);

        if(!$canEdit['canEdit']){
            return back()->with('error', 'You are not authorized to edit this report');
        }

        return view(
            'stakeholder.create',
            app(ReportService::class)->prepareEditData($report, $user, false)
            + compact('user')
        );
    }

    public function store(Request $request)
    {
        $stakeholder = Auth::guard('stakeholder')->user();
        $eligibleMonth = canAddReport($stakeholder->chapter_id);

        if(!($eligibleMonth['eligible'])) return redirect()->route('stakeholders.reports.index')->with('error', 'You cannot submit report for requested month.');
        $validated = app(ReportService::class)->validateRequest($request);
        $validated['month_number'] = $eligibleMonth['month_number'];
        $validated['year'] = $eligibleMonth['year'];

        $result = app(ReportService::class)->saveReport($stakeholder, null, $validated);

        return $result['status']
            ? redirect()->route('stakeholders.reports.index')->with('message', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function nudge(StakeholderReport $report)
    {
        $stakeholder = Auth::guard('stakeholder')->user();

        app(ReportService::class)->nudgeReportActors($stakeholder, $report);

        return redirect()
            ->route('stakeholders.reports.index')
            ->with('message', 'Report actors have been nudged successfully!');
    }


    public function update(Request $request, StakeholderReport $report)
    {
        $stakeholder = Auth::guard('stakeholder')->user();

        $validated = app(ReportService::class)->validateRequest($request);

        $result = app(ReportService::class)
            ->saveReport($stakeholder, $report, $validated);

        return $result['status']
            ? redirect()->route('stakeholders.reports.index')->with('message', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function show(StakeholderReport $report)
    {
        $data = app(ReportService::class)->prepareViewData($report, false);

        return view('reports.pdf_template', [
            'report'     => $report,
            'isAdmin'     => false,
            'sections'   => $data['sections']
        ]);

    }



    public function rejectReport(Request $request, StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();
        $reject = app(ReportService::class)->reject($user, $report);

        if($reject['status']){
            return redirect()
                ->route('stakeholders.reports.index')
                ->with('message', $reject['message'] ?? 'Report rejection recorded successfully!');
        }else{
            return redirect()
                ->route('stakeholders.reports.index')
                ->with('error', $approval['message'] ?? 'Report rejection could not be completed!');
        }
    }

    public function approveReport(StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();

        $approval = app(ReportService::class)->approve($user,$report);

        if($approval['status']){
            return redirect()
                ->route('stakeholders.reports.index')
                ->with('message', $approval['message'] ?? 'Report approved successfully!');
        }else{
            return redirect()
                ->route('stakeholders.reports.index')
                ->with('error', $approval['message'] ?? 'Report approval could not be completed!');
        }
    }


    public function destroy(StakeholderReport $reports)
    {
        //
    }

    public function download(StakeholderReport $report): BinaryFileResponse
    {
        $path = $report->file_location;

        abort_unless(file_exists($path), 404, 'Report file not found');

        return response()->download(
            $path,
            basename($path), // filename
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    public function financialReports(Request $request)
    {
        $user = Auth::guard('stakeholder')->user();
        $isAdmin = false;

        $result = app(ReportService::class)
            ->index($request, $user, $isAdmin);

        // If it's a download, the service will return a BinaryFileResponse
        if ($result instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            return $result;
        }

        // Otherwise, it's an array for the view
        return view('stakeholder.finance.index', array_merge($result, compact('user','isAdmin')));
    }


    public function financialReportsDownload(StakeholderReport $report){
        if($report){
            return app(ReportService::class)->downloadFinancialReport([$report]);
        }
    }
}
