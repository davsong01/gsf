<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\StakeholderReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\StakeholderQuestionSection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StakeholderReportsController extends Controller
{
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


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::guard('stakeholder')->user();
        $eligibleMonth = canAddReport($user);

        if(empty($eligibleMonth)) return back()->with('error', 'You cannot submit report for requested month.');

        $months = getMonths();
        $chapter = $user->chapter;

        $sections = StakeholderQuestionSection::isActive()->with([
            'subsections.questions' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('id')->get();

        if(!in_array(Auth::guard('stakeholder')->user()->role_id, chapterStakeholders())){
            return back()->with('error', 'Unauthorized Access');
        }

        $prefillData = [
            'chapter_name' => $chapter->name ?? '',
            'month' => date('m'),
            'year' => date('Y'),
            'year_established' => $chapter->year_established ?? '',
            'session' => date('Y') - 1 . '/'. date('Y'),
            'president_name' => '',
        ];

        $isAdmin = false;
        return view('stakeholder.create', compact('months', 'sections', 'prefillData', 'user','isAdmin'));
    }


    public function store(Request $request)
    {
        $stakeholder = Auth::guard('stakeholder')->user();

        $validated = $this->validateRequest($request);

        return $this->saveReport($stakeholder, null, $validated);
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

    public function edit(StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();
        $canEdit = app(\App\Services\ReportService::class)->canEditReport($report, $user);

        if(!$canEdit['canEdit']){
            return back()->with('error', 'You are not authorized to edit this report');
        }

        return view(
            'stakeholder.create',
            app(ReportService::class)->prepareEditData($report, $user, false)
            + compact('user')
        );
    }

    public function rejectReport(Request $request, StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();
        app(ReportService::class)->reject($user, $report);

        return redirect()
            ->route('stakeholders.reports.index')
            ->with('message', 'Report rejection recorded successfully!');
    }

    public function approveReport(StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();

        app(ReportService::class)->approve($user,$report);

        return redirect()
            ->route('stakeholders.reports.index')
            ->with('message', 'Report approved successfully!');
    }


    public function destroy(StakeholderReport $reports)
    {
        //
    }

    // public function delete($id){
    //     if(Auth::guard('stakeholder')->user()->role != 'Secretariat') return abort(404);
    //     $report =  StakeholderReport::find($id);
    //     if($report->stakeholderpayment){
    //         if (file_exists(base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image ))
    //             unlink( base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image );

    //             $report->stakeholderpayment->delete();
    //     }
    //     $report->delete();

    //     return back()->with('message', 'Report has been deleted forever!');
    // }

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
