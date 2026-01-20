<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Reports;
use App\Models\Setting;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use App\Services\ReportService;
use App\Models\StakeholderReport;
use Illuminate\Support\Facades\DB;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\StakeholderReportAnswer;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;
use App\Services\ReportNotificationService;
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
        $months = getMonths();
        $user = Auth::guard('stakeholder')->user();
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
        $comment = $request->rejection_reason;
        $role = $user->role->slug;

        switch ($role) {
            case 'zonal-pastor':
                $report->zone_comment = $comment;
                $report->zone_status = 2;
                $report->zone_rejected_at = now();
                // $report->zone_rejected_by = $user->id;
                break;

            case 'field-pastor':
                if ($report->zone_status !== 1) {
                    abort(403, 'Cannot reject before zone approval');
                }
                $report->field_comment = $comment;
                $report->field_status = 2;
                $report->field_rejected_at = now();
                // $report->field_rejected_by = $user->id;
                break;

            case 'secretariat':
            case 'ncp':
                if ($report->zone_status !== 1 || $report->field_status !== 1) {
                    abort(403, 'Cannot reject before zone and field approval');
                }
                $report->national_comment = $comment;
                $report->national_status = 2;
                $report->national_rejected_at = now();
                // $report->national_rejected_by = $user->id;
                break;

            default:
                abort(403, 'Unauthorized action');
        }

        $report->save();

        ReportNotificationService::handleReportAction($report, $user, 'reject');

        return redirect()
            ->route('stakeholders.reports.index')
            ->with('message', 'Report rejection recorded successfully!');
    }

    public function approveReport(StakeholderReport $report)
    {
        $user = Auth::guard('stakeholder')->user();
        // $comment = $request->rejection_reason ?? null;
        $roleSlug = $user->role->slug;

        switch ($roleSlug) {
            case 'zonal-pastor':
                // $report->zone_comment = $comment;
                $report->zone_status = 1; // Approved
                $report->zone_approved_at = now();
                // $report->zone_approved_by = $user->id;
                break;

            case 'field-pastor':
                if ($report->zone_status !== 1) {
                    abort(403, 'Cannot approve before zone approval');
                }
                // $report->field_comment = $comment;
                $report->field_status = 1;
                $report->field_approved_at = now();
                // $report->field_approved_by = $user->id;
                break;

            case 'secretariat':
            case 'ncp':
                if ($report->zone_status !== 1 || $report->field_status !== 1) {
                    abort(403, 'Cannot approve before zone and field approval');
                }
                // $report->national_comment = $comment;
                $report->national_status = 1;
                $report->national_approved_at = now();
                // $report->national_approved_by = $user->id;
                break;

            default:
                abort(403, 'Unauthorized action');
        }

        $report->save();

        ReportNotificationService::handleReportAction($report, $user, 'approve');

        return redirect()
            ->route('stakeholders.reports.index')
            ->with('message', 'Report approved successfully!');
    }


    public function destroy(StakeholderReport $reports)
    {
        //
    }

    public function delete($id){
        if(Auth::guard('stakeholder')->user()->role != 'Secretariat') return abort(404);
        $report =  StakeholderReport::find($id);
        if($report->stakeholderpayment){
            if (file_exists(base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image ))
                unlink( base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image );

                $report->stakeholderpayment->delete();
        }
        $report->delete();

        return back()->with('message', 'Report has been deleted forever!');
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

}
