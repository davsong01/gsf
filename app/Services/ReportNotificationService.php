<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Payment;
use App\Models\ConferenceEdition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportNotificationService
{
    
    public static function handleReportSubmissionSubmission($report, $stakeholder){
        // depending on who the stakeholder is, we will send notification email and attach the pdf of the report to the email with instructions to act on it and a link to login to act on it

        // Send notification email
        // if ($report->zone && $report->zone->stakeholder) {
        //     $mailData = [
        //         'type' => 'zone',
        //         'addressee' => $report->zone->stakeholder->name,
        //         'chapter' => $report->chapter->name,
        //         'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year,
        //     ];

        //     Mail::to($report->zone->stakeholder->email)->send(new NotificationEmail($mailData));
        // }


    }
}
