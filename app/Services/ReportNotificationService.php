<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Stakeholder;
use Illuminate\Support\Str;
use App\Services\EmailService;
use Pdf;
use App\Models\StakeholderReport;
use App\Models\StakeholderQuestionSection;

class ReportNotificationService
{
    public static function handleReportSubmission($report, $stakeholder, $type)
    {
        if (!in_array($type, ['update', 'store'], true)) {
            return;
        }

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

    public static function handleReportAction(StakeholderReport $report, $stakeholder,  string $action)
    {
        $pdfFilePath = $report->file_location;
        $loginLink = "<a href='" . url('/stakeholders/login') . "'>Login</a>";

        if($action != 'nudge'){
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
                if (in_array($stakeholder->role_id, $roleHierarchy[$level], true)) {
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

            switch ($stakeholder->role->slug) {
                case 'zonal-pastor':
                    $levelLabel = 'Zonal Level';
                    $comment = $report->zone_comment;
                    break;
                case 'field-pastor':
                    $levelLabel = 'Field Level';
                    $comment = $report->field_comment;
                    break;
                case 'secretariat':
                case 'ncp':
                    $levelLabel = 'National Level';
                    $comment = $report->national_comment;
                    break;
            }

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
                        ->values()
                        ->toArray();

                    if(!empty($nextLevelRecipients)){
                        foreach($nextLevelRecipients as $recipient){
                            $generatedEmail = self::generateReportEmailSummary($report, $stakeholder, $recipient, $action, $levelLabel);

                            $allEmailData[] = [
                                'recipient' => $recipient['email'],
                                'type'      => 'report_email',
                                'subject'   => $generatedEmail['subject'],
                                'content'   => $generatedEmail['content'].  "<p>Kindly {$loginLink} to review the report and perform the necessary actions.</p>",
                                'attachments' => json_encode([
                                    $pdfFilePath
                                ]),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            }

            /**
             * NOTIFY ALL LOWER LEVELS
             */
            if ($action === 'reject') {
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
                        ->values()
                        ->toArray();

                    if (!empty($recipientsBelow)) {
                        if ($levelLabel == 'Zonal Level') {
                            $rejectionReason = $report->zone_comment;
                        } elseif ($levelLabel == 'Field Level') {
                            $rejectionReason = $report->field_comment;
                        } elseif ($levelLabel == 'National Level') {
                            $rejectionReason = $report->zone_comment;
                        }

                        $reason = '';

                        if (!empty($rejectionReason)) {
                            $reason = "<h4>Rejection Reason</h4>" . $rejectionReason;
                        }

                        foreach($recipientsBelow as $recipient){
                            $generatedEmail = self::generateReportEmailSummary($report, $stakeholder, $recipient, $action, $levelLabel);

                            $allEmailData[] = [
                                'recipient' => $recipient['email'],
                                'type'      => 'report_email',
                                'subject'   => $generatedEmail['subject'],
                                'content'   => $generatedEmail['content']. $reason,
                                'attachments' => json_encode([
                                    $pdfFilePath
                                ]),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                        }
                    }
                }
            }
        }else {
            // Pending levels in order of responsibility
            $pendingLevels = [
                'zone'     => $report->zone_status,
                'field'    => $report->field_status,
                'national' => $report->national_status,
            ];

            // Map levels to roles
            $levelRoleMap = [
                'zone'     => zoneStakeholders(),
                'field'    => fieldStakeholders(),
                'national' => array_merge(secretariatStakeholders(), ncpStakeholders()),
            ];

            foreach ($pendingLevels as $level => $status) {
                if ($status === 0) { // first pending level found
                    $roles = $levelRoleMap[$level];

                    $recipients = Stakeholder::select(['name', 'email', 'role_id','zone_id','field_id'])
                        ->where('status', 'active')
                        ->whereIn('role_id', $roles);

                    // Scope by location
                    if ($level === 'zone')  $recipients->where('zone_id', $report->zone_id);
                    if ($level === 'field') $recipients->where('field_id', $report->field_id);

                    $recipients = $recipients->get()
                        ->filter(fn($s) => !empty($s->email))
                        ->unique('email')
                        ->values()
                        ->toArray();

                    foreach ($recipients as $recipient) {
                        $generatedEmail = self::generateReportEmailSummary(
                            $report,
                            null, // ignore actor for nudge
                            $recipient,
                            'nudge',
                            ucfirst($level).' Level'
                        );

                        $allEmailData[] = [
                            'recipient'   => $recipient['email'],
                            'type'        => 'report_email',
                            'subject'     => $generatedEmail['subject'],
                            'content'     => $generatedEmail['content'] . "<p>Kindly {$loginLink} to complete your section of the report.</p>",
                            'attachments' => json_encode([$pdfFilePath]),
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ];
                    }

                    // Stop after sending to the first pending level
                    break;
                }
            }


        }

        $emailData = [
            'type'        => 'report_email',
            'recipients' => $allEmailData,
        ];

        EmailService::logEmail($emailData);

        return 'All sent';
    }

    public static function generateReportEmailSummary($report, $stakeholder, $recipient, $type = 'store', $currentLevel=null): array
    {
        $monthName = Carbon::create()
            ->month($report->month)
            ->format('F');

        $emailMap = [
            'store' => [
                'opening' => "GSF ({$monthName}, {$report->year}) Monthly Report submitted. Please find details below:",
                'subject' => "GSF ({$monthName}, {$report->year}) Monthly Report Submitted",
            ],
            'update' => [
                'opening' => "GSF ({$monthName}, {$report->year}) Monthly Report updated. Please find details below:",
                'subject' => "GSF ({$monthName}, {$report->year}) Monthly Report Updated",
            ],
            'approve' => [
                'opening' => "GSF ({$monthName}, {$report->year}) Monthly Report approved at {$currentLevel}. Please find details below:",
                'subject' => "GSF ({$monthName}, {$report->year}) Monthly Report Approved at {$currentLevel}",
            ],
            'reject' => [
                'opening' => "GSF ({$monthName}, {$report->year}) Monthly Report rejected at {$currentLevel}. Please find details below:",
                'subject' => "GSF ({$monthName}, {$report->year}) Monthly Report Rejected at {$currentLevel}",
            ],
            'nudge' => [
                'opening' => "Reminder: Please take action on the GSF ({$monthName}, {$report->year}) monthly report",
                'subject' => "Action Required: GSF ({$monthName}, {$report->year}) Report",
            ],
        ];

        $actorLabel = match ($type) {
            'approve' => 'Approved by',
            'reject'  => 'Rejected by',
            default   => 'Submitted by',
        };

        // Fallback safety
        $config  = $emailMap[$type] ?? $emailMap['store'];
        $opening = $config['opening'];
        $subject = $config['subject'];
        $extra = $config['extra'] ?? '';

        $statuses = [
            'Zone'     => $report->zone_status,
            'Field'    => $report->field_status,
            'National' => $report->national_status,
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
        $name = $stakeholder ? $stakeholder->name : ($recipient['name'] ?? 'User');

        $content = "
            <h4>{$salutation}</h4>

            <p>{$opening}</p>

            <p>Chapter: <strong>{$report->chapter->name}</strong></p>";

        if($type != 'nudge'){
            $content .= "
            <p>{$actorLabel}: <strong>{$name}</strong></p>";
        }

        $content .= "
            <p>Zone: <strong>{$report->chapter->zone->name}</strong></p>
            <p>Field: <strong>{$report->chapter->field->name}</strong></p>
            <p>Submission Date:
                <strong>{$report->updated_at->format('d M Y H:i')}</strong>
            </p>

            <hr>

            <h4>Status Overview</h4>
            {$statusHtml}";

            $content .= "<p>
                    For full report details, please download the attached PDF.
                </p>
            ". $extra;

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
        $isAdmin = false;

        if ($preview) {
            return view('reports.pdf_template', compact(
                'report',
                'isAdmin',
                'reportData',
                'sections'
            ));
        }

        /**
         * Generate PDF
         */
        $pdf = Pdf::loadView('reports.pdf_template', compact(
            'report',
            'isAdmin',
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
