<?php

namespace App\Services;

use App\Models\Stakeholder;
use Illuminate\Support\Str;
use App\Services\EmailService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use App\Services\FileUploadService;
use App\Models\StakeholderQuestionSection;

class ReportNotificationService
{

    public static function handleReportSubmission($report, $stakeholder, $type)
    {
        if (!in_array($type, ['update', 'store'], true)) {
            return;
        }

        /**
         * ROLE HIERARCHY (LOW → HIGH)
         * Order here defines authority, NOT role_id values
         */
        $roleHierarchy = [
            'chapter'     => chapterStakeholders(),
            'zone'        => zoneStakeholders(),
            'field'       => fieldStakeholders(),
            'secretariat' => secretariatStakeholders(),
            'ncp'         => ncpStakeholders(),
        ];

        $levels = array_keys($roleHierarchy);

        /**
         * Resolve current stakeholder hierarchy index
         */
        $currentLevelIndex = null;
        
        foreach ($levels as $index => $level) {
            if (in_array($stakeholder->role_id, $roleHierarchy[$level], true)) {
                $currentLevelIndex = $index;
                break;
            }
        }
        if ($currentLevelIndex === null) {
            return;
        }
        

        /**
         * Collect recipients ABOVE current level
         */
        $recipients = collect();

        for ($i = $currentLevelIndex + 1; $i < count($levels); $i++) {
            $level = $levels[$i];
            
            $query = Stakeholder::select(['name', 'email','role_id'])->where('status', 'active')->whereIn('role_id', $roleHierarchy[$level]);
            
            // Chapter-scoped hierarchy levels
            if (in_array($level, ['chapter'])) {
                $query->where('chapter_id', $report->chapter_id);
            }

            if (in_array($level, ['zone'])) {
                $query->where('zone_id', $report->zone_id);
            }

            if (in_array($level, ['field'])) {
                $query->where('field_id', $report->field_id);
            }
            
            $recipients = $recipients->merge($query->get());
        }

        /**
         * De-duplicate & extract emails
         */
        $recipients = $recipients
            ->filter(fn($s) => !empty($s->email))
            ->unique('email')
            ->pluck('email')
            ->values()
            ->toArray();

        if (empty($recipients)) {
            return;
        }

        /**
         * Generate PDF attachment
         */
        // Later delete all generated reports
        $pdfFilePath = self::generatePdf($report);
        
        /**
         * Email content
         */
        $subject = $type === 'store'
            ? "New Report Submitted: {$report->title}"
            : "Report Updated: {$report->title}";
        
        $emailData = [
            'type'        => 'report.update',
            'subject'     => $subject,
            'content'     => self::generateReportEmailSummary($report, $stakeholder, $type),
            'attachments' => [$pdfFilePath],
            'recipients'  => $recipients,
        ];
        
        EmailService::logEmail($emailData);
    }



    public static function generateReportEmailSummary($report, $stakeholder, $type = 'store')
    {

        if($type === 'store'){
            $opening = 'New GSF Report submitted, please find details below:';
        }
        if ($type === 'update') {
            $opening = 'GSF Report updated, please find details below:';
        }

        $html = "<h2>Dear {$stakeholder->name}</h2>
        <p>{$opening}</p>

        <p>Submitted by: <strong>{$stakeholder->name}</strong></p>
        <p>Chapter: <strong>{$report->chapter->name}</strong></p>
        <p>Zone: <strong>{$report->chapter->zone->name}</strong></p>
        <p>Field: <strong>{$report->chapter->field->name}</strong></p>
        <p>Submission Date: <strong>{$report->updated_at->format('d M Y H:i')}</strong></p>
        <hr>
        
        <p>For full report details, please download the attached PDF.</p>";
        
        return $html;
    }


    public static function generatePdf($report, $preview = false): mixed
    {
        // Prepare report data
        $reportData = $report->answers->mapWithKeys(function ($answer) {
            $decoded = json_decode($answer->answer_value, true);
            return [$answer->question->label => $decoded ?? $answer->answer_value];
        });

        // Get sections & questions
        $sections = StakeholderQuestionSection::isActive()->with([
            'subsections.questions' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('id')->get();

        if ($preview) {
            // Return the Blade view directly so the browser renders HTML
            return view('reports.pdf_template', [
                'report'     => $report,
                'reportData' => $reportData,
                'sections'   => $sections
            ]);
        }

        // Generate PDF
        $pdf = Pdf::loadView('reports.pdf_template', [
            'report'     => $report,
            'reportData' => $reportData,
            'sections'   => $sections
        ])->setPaper('a4', 'portrait');

        $pdfBinary = $pdf->output();

        // Generate PDF file name
        $pdfName = Str::slug($report->chapter->name . ' ' . date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year . ' report');

        // Temporary file path
        $tmpPath = sys_get_temp_dir() . '/' . $pdfName . '.pdf';
        
        file_put_contents($tmpPath, $pdfBinary);

        // Convert to UploadedFile
        $uploadedFile = new UploadedFile(
            $tmpPath,
            'report_' . $report->id . '.pdf',
            'application/pdf',
            null,
            true // mark as test file to bypass isValid() check
        );

        // Build folder structure: reports/{year}/{month}
        $monthName = date('F', mktime(0, 0, 0, $report->month, 10)); // e.g., 12 → December
        $folderPath = 'reports/' . $report->year . '/' . $monthName;
        // Upload via service
        $filePath = FileUploadService::publicUpload(
            $uploadedFile,
            $folderPath,
            '',
            $pdfName . '.pdf',
        );
        
        return $filePath;
    }
}
