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
use Spatie\FlareClient\Report;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminReportsController extends Controller
{
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

        StakeholderReport::with(['stakeholder:id,zone_id,field_id', 'chapter:id,zone_id,field_id'])
            ->where(function ($q) {
                $q->whereNull('zone_id')
                ->orWhereNull('field_id');
            })
            ->chunk(200, function ($reports) use (&$count) {

                foreach ($reports as $report) {

                    $zoneId = $report->stakeholder?->zone_id ?? $report->chapter?->zone_id;
                    $fieldId = $report->stakeholder?->field_id ?? $report->chapter?->field_id;

                    if ($zoneId || $fieldId) {
                        $report->update([
                            'zone_id'  => $zoneId,
                            'field_id' => $fieldId,
                        ]);
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
}
