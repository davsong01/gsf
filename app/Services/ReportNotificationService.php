<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Stakeholder;
use Illuminate\Support\Str;
use App\Services\EmailService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StakeholderReport;
use Illuminate\Http\UploadedFile;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Auth;
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

        $recipients = $recipients
            ->filter(fn($s) => !empty($s->email))
            ->unique('email')
            ->values()
            ->toArray();
        
        if (empty($recipients)) {
            return;
        }

        $pdfFilePath = self::generatePdf($report);
        $report->update(['file_location' => $pdfFilePath['relative_path'] ?? null]);

        $allEmailData = [];

        foreach ($recipients as $recipient) {
            $generatedEmail = self::generateReportEmailSummary($report, $stakeholder, $recipient, $type);
            $allEmailData[] = [
                'recipient' => $recipient['email'],
                'type'      => 'report_email',
                'subject'   => $generatedEmail['subject'],
                'content'   => $generatedEmail['content'],
                'attachments' => json_encode([
                    $pdfFilePath['relative_path'] ?? null
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        $emailData = [
            'type'        => 'report_email',
            'recipients' => $allEmailData,
        ];
        
        EmailService::logEmail($emailData);

        return;
    }

    public static function handleReportAction(StakeholderReport $report, string $action)
    {
        $user = Auth::guard('stakeholder')->user();
        if (!$user || !in_array($action, ['approve', 'reject'], true)) return;

        /**
         * ROLE HIERARCHY (LOW → HIGH)
         */
        $roleHierarchy = [
            'chapter'     => chapterStakeholders(),
            'zone'        => zoneStakeholders(),
            'field'       => fieldStakeholders(),
            'secretariat' => secretariatStakeholders(),
            'ncp'         => ncpStakeholders(),
        ];

        $levels = array_keys($roleHierarchy);

        // Determine current stakeholder level
        $currentLevelIndex = null;
        foreach ($levels as $index => $level) {
            if (in_array($user->role_id, $roleHierarchy[$level], true)) {
                $currentLevelIndex = $index;
                break;
            }
        }
        if ($currentLevelIndex === null) return;

        /**
         * Determine level label and comment
         */
        $levelLabel = '';
        $comment = '';
        switch ($user->role) {
            case 'Zonal Pastor':
                $levelLabel = 'Zone';
                $comment = $report->zone_comment;
                break;
            case 'Field Pastor':
                $levelLabel = 'Field';
                $comment = $report->field_comment;
                break;
            case 'Secretariat':
            case 'NCP':
                $levelLabel = 'National';
                $comment = $report->national_comment;
                break;
        }

        /**
         * Use existing PDF file
         */
        $pdfFilePath = $report->file_location;

        /**
         * NOTIFY NEXT LEVEL (approval only)
         */
        if ($action === 'approve') {
            $nextLevelIndex = $currentLevelIndex + 1;
            if (isset($levels[$nextLevelIndex])) {
                $nextLevel = $levels[$nextLevelIndex];

                $query = Stakeholder::select(['name', 'email', 'role_id'])
                    ->where('status', 'active')
                    ->whereIn('role_id', $roleHierarchy[$nextLevel]);

                // Scope based on report location
                if ($nextLevel === 'chapter') $query->where('chapter_id', $report->chapter_id);
                if ($nextLevel === 'zone')    $query->where('zone_id', $report->zone_id);
                if ($nextLevel === 'field')   $query->where('field_id', $report->field_id);

                $nextLevelRecipients = $query->get()
                    ->filter(fn($s) => !empty($s->email))
                    ->unique('email')
                    ->pluck('email')
                    ->values()
                    ->toArray();

                if (!empty($nextLevelRecipients)) {
                    $subject = "GSF Report Ready — {$levelLabel} Approved";
                    $content = "<p>Dear Stakeholder,</p>
                            <p>The report for <strong>{$report->chapter->name}</strong> has been <strong>approved</strong> at the <strong>{$levelLabel}</strong> level.</p>
                            <p>It is now ready for your action at the <strong>{$nextLevel}</strong> level.</p>
                            <p>For full details, please see the attached report.</p>";

                    EmailService::logEmail([
                        'type'        => 'report_email',
                        'subject'     => $subject,
                        'content'     => $content,
                        'attachments' => [$pdfFilePath],
                        'recipients'  => $nextLevelRecipients,
                    ]);
                }
            }
        }

        /**
         * NOTIFY ALL LOWER LEVELS
         */
        if ($currentLevelIndex > 0) {
            $recipientsBelow = collect();
            for ($i = 0; $i < $currentLevelIndex; $i++) {
                $level = $levels[$i];

                $query = Stakeholder::select(['name', 'email', 'role_id'])
                    ->where('status', 'active')
                    ->whereIn('role_id', $roleHierarchy[$level]);

                // Scope based on report location
                if ($level === 'chapter') $query->where('chapter_id', $report->chapter_id);
                if ($level === 'zone')    $query->where('zone_id', $report->zone_id);
                if ($level === 'field')   $query->where('field_id', $report->field_id);

                $recipientsBelow = $recipientsBelow->merge($query->get());
            }

            $recipientsBelow = $recipientsBelow
                ->filter(fn($s) => !empty($s->email))
                ->unique('email')
                ->pluck('email')
                ->values()
                ->toArray();

            if (!empty($recipientsBelow)) {
                $actionLabel = ucfirst($action);
                $subject = "GSF Report {$actionLabel} — {$levelLabel} Level";
                $content = "<p>Dear Stakeholder,</p>
                        <p>The report for <strong>{$report->chapter->name}</strong> has been <strong>{$actionLabel}d</strong> at the <strong>{$levelLabel}</strong> level.</p>";

                if ($action === 'reject' && $comment) {
                    $content .= "<p><strong>Reason:</strong> {$comment}</p>";
                } elseif ($action === 'approve') {
                    $content .= "<p>This indicates the report has successfully passed the <strong>{$levelLabel}</strong> level.</p>";
                }

                $content .= "<p>For full details, please see the attached report.</p>";

                EmailService::logEmail([
                    'type'        => 'report_email',
                    'subject'     => $subject,
                    'content'     => $content,
                    'attachments' => [$pdfFilePath],
                    'recipients'  => $recipientsBelow,
                ]);
            }
        }
    }
    
    public static function generateReportEmailSummary($report, $stakeholder, $recipient, $type = 'store'): array
    {
        $monthName = Carbon::create()
            ->month($report->month)
            ->format('F');

        $opening = match ($type) {
            'update' => "GSF ({$monthName}, {$report->year}) Report updated. Please find details below:",
            default  => "GSF ({$monthName}, {$report->year}) Report submitted. Please find details below:",
        };

        $subject = $type === 'store'
            ? "GSF ({$monthName}, {$report->year}) Report Submitted"
            : "GSF ({$monthName}, {$report->year}) Report Updated";

        $statuses = [
            'Zone'     => $report->zone_status,
            'Field'    => $report->field_status,
            'National' => $report->status_complete,
        ];

        $statusHtml = '';

        foreach ($statuses as $key => $status) {
            if ($status == 1) {
                $label = '<span style="color:green;font-weight:bold;">Approved</span>';
            } elseif ($status == 2) {
                $label = '<span style="color:red;font-weight:bold;">Rejected</span>';
            } else {
                $label = '<span style="color:orange;font-weight:bold;">Pending</span>';
            }

            $statusHtml .= "<p><strong>{$key}:</strong> {$label}</p>";
        }
        $salutation = !empty($recipient['name']) ? 'Dear '. $recipient['name'] .',': '';
        
        $content = "
            <h4>{$salutation}</h4>

            <p>{$opening}</p>

            <p>Chapter: <strong>{$report->chapter->name}</strong></p>
            <p>Submitted by: <strong>{$stakeholder->name}</strong></p>
            <p>Zone: <strong>{$report->chapter->zone->name}</strong></p>
            <p>Field: <strong>{$report->chapter->field->name}</strong></p>
            <p>Submission Date: 
                <strong>{$report->updated_at->format('d M Y H:i')}</strong>
            </p>

            <hr>

            <h4>Status Overview</h4>
            {$statusHtml}

            <p>
                For full report details, please download the attached PDF.
            </p>
        ";

        return [
            'subject' => $subject,
            'content' => $content,
        ];
    }

    public static function generatePdf($report, bool $preview = false): mixed
    {
        // Prepare report data
        $reportData = $report->answers->mapWithKeys(function ($answer) {
            $decoded = json_decode($answer->answer_value, true);
            return [$answer->question->label => $decoded ?? $answer->answer_value];
        });

        // Load sections & questions
        $sections = StakeholderQuestionSection::isActive()
            ->with(['subsections.questions' => fn($q) => $q->orderBy('order')])
            ->orderBy('id')
            ->get();

        /**
         * PREVIEW MODE → Render HTML in browser
         */
        if ($preview) {
            return view('reports.pdf_template', compact(
                'report',
                'reportData',
                'sections'
            ));
        }

        /**
         * Generate PDF
         */
        $pdf = Pdf::loadView('reports.pdf_template', compact(
            'report',
            'reportData',
            'sections'
        ))->setPaper('a4', 'portrait');

        $pdfBinary = $pdf->output();

        /**
         * File naming
         */
        $monthName = date('F', mktime(0, 0, 0, $report->month, 10));
        $fileName  = Str::slug(
            "{$report->chapter->name} {$monthName} {$report->year} report"
        ) . '.pdf';

        /**
         * Folder: public/reports/{year}/{Month}
         */
        $relativeDir  = "reports/{$report->year}/{$monthName}";
        $absoluteDir  = public_path($relativeDir);

        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0755, true);
        }

        /**
         * Final absolute path
         */
        $absolutePath = "{$absoluteDir}/{$fileName}";

        file_put_contents($absolutePath, $pdfBinary);

        /**
         * RETURN PATH (NOT URL)
         * This is what mail attachments need
         */
        return [
            'absolute_path' => $absolutePath,
            'relative_path' => "{$relativeDir}/{$fileName}",
            'filename'      => $fileName,
        ];
    }


}
