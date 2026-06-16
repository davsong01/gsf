<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Stakeholder;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderQuestionSubSection;
use App\Models\StakeholderReport;
use App\Models\Zone;
use App\Services\ExcelService;
use App\Services\ReportAnalyticsService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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

    public function toggleEditMode(Request $request)
    {
        $request->validate([
            'report_id'  => 'required|integer|exists:stakeholder_reports,id',
            'edit_mode'  => 'required|boolean',
        ]);

        $report = StakeholderReport::findOrFail($request->report_id);

        $report->edit_mode = (int) $request->edit_mode;
        $report->save();

        return response()->json([
            'status'  => true,
            'message' => $report->edit_mode
                ? 'Edit mode enabled'
                : 'Edit mode disabled',
        ]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $user->role_id = $user->role;

        $isAdmin = true;
        $data = app(ReportService::class)
            ->index($request, $user, $isAdmin);

        if ($data instanceof \Illuminate\Http\Response ||
            $data instanceof \Symfony\Component\HttpFoundation\Response) {
            return $data;
        }
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


    // public function reportAnalyticsType(Request $request, $type)
    // {
    //     $isAdmin = true;
    //     $user = auth()->user();

    //     $scope = app(ReportService::class)->getScopedEntitiesForUser($user, $isAdmin);

    //     $fields = DB::table('fields')->orderBy('name')->get();
    //     $zones  = DB::table('zones')->orderBy('name')->get();

    //     $data = [
    //         'level' => $request->level ?? 'chapter',
    //         'type'  => $type,
    //         'fields'  => $fields,
    //         'zones'  => $zones,
    //         'legends' => Chapter::orderBy('name')->get(),
    //         'isAdmin' => isAdmin()['status'],// for filters
    //     ];

    //     $request['isAdmin'] = $isAdmin;

    //     if ($request->filter_type === 'excel') {
    //         $result = $this->reportAnalyticService->fetchAnalyticsTypeData($request, $scope);

    //         $labels = $result['labels'];
    //         $datasets = $result['datasets'];

    //         $exportData = [];

    //         foreach ($datasets as $chapterData) {

    //             $chapter = Chapter::with(['field', 'zone'])->find($chapterData['legend_id']);

    //             $row = [
    //                 'Chapter' => $chapterData['label'],
    //                 'Field'   => $chapter->field->name ?? '-',
    //                 'Zone'    => $chapter->zone->name ?? '-',
    //             ];

    //             foreach ($labels as $index => $month) {
    //                 // Use tooltip (status label) for each month
    //                 $row[$month] = $chapterData['tooltip'][$index][0]['status_label'] ?? 'Not Submitted';
    //             }

    //             $exportData[] = $row;
    //         }

    //         // Build headers with Field and Zone included
    //         $headers = array_merge(['Chapter', 'Field', 'Zone'], $labels);

    //         // Download Excel
    //         return ExcelService::download(
    //             $exportData,
    //             $headers,
    //             'chapter_compliance_report.xlsx'
    //         );
    //     }

    //     if ($request->filter_type === 'pdf') {
    //         $reportService = app(ReportAnalyticsService::class);

    //         $data = $reportService->generateSubmissionStatusReport(
    //             $scope,
    //             $request->from_date ?? null,
    //             $request->to_date ?? null
    //         );

    //         return $reportService->downloadSubmissionStatusPdf(
    //             $data,
    //             $request
    //         );
    //     }

    //     // Handle AJAX request for graph
    //     if ($request->isMethod('post')) {
    //         $result = $this->reportAnalyticService->fetchAnalyticsTypeData($request, $scope);
    //         return response()->json([
    //             'labels'        => $result['labels'],
    //             'datasets'      => $result['datasets'],
    //             'status_levels' => $result['status_levels'],
    //         ]);
    //     }

    //     // Normal GET request to view page
    //     return view('admin.reports.analytics.compliance', $data);
    // }
    public function reportAnalyticsType(Request $request, $type)
    {
        $isAdmin = true;
        $user = auth()->user();

        $scope = app(ReportService::class)
            ->getScopedEntitiesForUser($user, $isAdmin);

        $fields = Field::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();

        $data = [
            'level' => $request->level ?? 'chapter',
            'type' => $type,
            'fields' => $fields,
            'zones' => $zones,
            'sections' => StakeholderQuestionSection::orderBy('name')->get(),
            'chapters' => Chapter::orderBy('name')->get(),
            'legends' => Chapter::orderBy('name')->get(),
            'isAdmin' => isAdmin()['status'],
        ];

        if ($type === 'section') {

            if ($request->filter_type === 'excel') {

                $exportData = $this->reportAnalyticService
                    ->getQuestionAnalysisData(
                        $request
                    );
                    
                return ExcelService::download(
                    $exportData['rows'],
                    $exportData['headers'],
                    'question_analysis_report.xlsx'
                );
            }

            $data['reports'] = $this->reportAnalyticService->getQuestionAnalysisData($request);

            return view(
                'admin.reports.analytics.question-analysis',
                $data
            );
        }

        $request['isAdmin'] = $isAdmin;

        if ($request->filter_type === 'excel') {

            $result = $this->reportAnalyticService
                ->fetchAnalyticsTypeData($request, $scope);

            $labels = $result['labels'];
            $datasets = $result['datasets'];

            $exportData = [];

            foreach ($datasets as $chapterData) {

                $chapter = Chapter::with([
                    'field',
                    'zone'
                ])->find($chapterData['legend_id']);

                $row = [
                    'Chapter' => $chapterData['label'],
                    'Field' => $chapter->field->name ?? '-',
                    'Zone' => $chapter->zone->name ?? '-',
                ];

                foreach ($labels as $index => $month) {
                    $row[$month] = $chapterData['tooltip'][$index][0]['status_label']
                        ?? 'Not Submitted';
                }

                $exportData[] = $row;
            }

            $headers = array_merge(
                ['Chapter', 'Field', 'Zone'],
                $labels
            );

            return ExcelService::download(
                $exportData,
                $headers,
                'chapter_compliance_report.xlsx'
            );
        }

        if ($request->filter_type === 'pdf') {

            $reportService = app(ReportAnalyticsService::class);

            $pdfData = $reportService->generateSubmissionStatusReport(
                $scope,
                $request->from_date,
                $request->to_date
            );

            return $reportService->downloadSubmissionStatusPdf(
                $pdfData,
                $request
            );
        }

        if ($request->isMethod('post')) {

            $result = $this->reportAnalyticService
                ->fetchAnalyticsTypeData($request, $scope);

            return response()->json([
                'labels' => $result['labels'],
                'datasets' => $result['datasets'],
                'status_levels' => $result['status_levels'],
            ]);
        }

        return view(
            'admin.reports.analytics.compliance',
            $data
        );
    }

    public function getSubSectionsBySections(Request $request)
    {
        $query = StakeholderQuestionSubSection::query();

        if (
            $request->filled('sections') &&
            !in_array('all', (array) $request->sections)
        ) {
            $query->whereIn('section_id', $request->sections);
        }

        $subSections = $query
            ->orderBy('name')
            ->get(['id', 'name']);

        // prepend "All"
        return response()->json(
            collect([
                [
                    'id' => 'all',
                    'name' => 'All'
                ]
            ])->merge($subSections)->values()
        );
    }
    public function getQuestionAnalysisData(Request $request): array
    {
        $query = StakeholderReport::query()
            ->with([
                'stakeholder',
                'chapter',
                'zone',
                'field'
            ]);

        if ($request->filled('chapters')) {
            $query->whereIn('chapter_id', $request->chapters);
        }

        if ($request->filled('fields')) {
            $query->whereIn('field_id', $request->fields);
        }

        if ($request->filled('zones')) {
            $query->whereIn('zone_id', $request->zones);
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );
        }

        $reports = $query->get();

        return [
            'headers' => [
                'Institution',
                'Chapter',
                'Zone',
                'Field',
                'Section',
                'Sub Section',
                'Question',
                'Answer',
            ],
            'rows' => [],
        ];
    }

    public function adjustReportStatus(Request $request, StakeholderReport $report){
        $status = $request->approval_status;

        $statusMap = [
            'zone_approved'     => fn () => $report->chapter?->zone?->zonalCord,
            'zone_rejected'     => fn () => $report->chapter?->zone?->zonalCord,

            'field_approved'    => fn () => $report->chapter?->field?->fieldCord,
            'field_rejected'    => fn () => $report->chapter?->field?->fieldCord,

            'national_approved' => fn () => Stakeholder::whereIn('role_id', secretariatStakeholders())->first(),
            'national_rejected' => fn () => Stakeholder::whereIn('role_id', secretariatStakeholders())->first(),
        ];

        if (!isset($statusMap[$status])) {
            abort(400, 'Invalid approval status');
        }

        $user = $statusMap[$status]();

        if (!$user) {
            abort(404, 'Approver not found');
        }

        $service = app(ReportService::class);

        if (str_contains($status, 'approved')) {
            $service->approve($user, $report);
        } else {
            $service->reject($user, $report);
        }

        return back()->with('success', 'Report status updated successfully');
    }

}
