<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Models\ConferenceEdition;
use App\Models\StakeholderReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\StakeholderReportAnswer;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;
use App\Services\ReportNotificationService;

class ReportService
{
    public function sessionRange(): array
    {
        $sessions = [];
        $currentYear = (int) date('Y');

        for ($year = 2023; $year <= $currentYear; $year++) {
            $session = $year . '/' . ($year + 1);
            $sessions[] = [
                'label' => $session,
                'value' => $session,
            ];
        }

        return array_reverse($sessions);
    }

    public function index(Request $request, $user, bool $isAdmin = false)
    {
        $role = $user->role_id ?? $user->role;

        /** =====================
         * BASE MODELS
         * ===================== */
        $chaptersQuery = Chapter::query();
        $zonesQuery = Zone::query();
        $fieldsQuery = Field::query();

        $chapterIds = collect();
        $zoneIds = collect();
        $fieldIds = collect();

        /** =====================
         * ROLE-BASED SCOPING
         * ===================== */
        if ($isAdmin || finStakeholders($user)) {
            // Admin → full access
            $chapterIds = Chapter::pluck('id');
            $zoneIds    = Zone::pluck('id');
            $fieldIds   = Field::pluck('id');
        } else {
            if (in_array($role, chapterStakeholders())) {
                $chapterIds = collect([$user->chapter_id]);
                $zoneIds    = collect([$user->zone_id]);
                $fieldIds   = collect([$user->field_id]);
            }
            elseif (in_array($role, zoneStakeholders())) {
                $zoneIds = collect([$user->zone_id]);

                $chapterIds = Chapter::where('zone_id', $user->zone_id)->pluck('id');

                $fieldIds = Field::whereHas('zones', fn ($q) =>
                    $q->where('id', $user->zone_id)
                )->pluck('id');
            }
            elseif (in_array($role, fieldStakeholders())) {
                $fieldIds = collect([$user->field_id]);

                $zoneIds = Zone::where('field_id', $user->field_id)->pluck('id');

                $chapterIds = Chapter::whereIn('zone_id', $zoneIds)->pluck('id');
            }
            elseif (in_array($role, secretariatStakeholders())) {
                $chapterIds = Chapter::pluck('id');
                $zoneIds    = Zone::pluck('id');
                $fieldIds   = Field::pluck('id');
            }
        }

        /** =====================
         * REPORT QUERY
         * ===================== */

        if(finStakeholders($user)) {
            $finIds = finSubSectionIds();

            $reports = StakeholderReport::whereHas('answers', function ($query) use ($finIds) {
                $query->whereHas('question', function ($q) use ($finIds) {
                    $q->whereHas('subsection', function ($q) use ($finIds) {
                        $q->whereIn('id', $finIds);
                        });
                });
            });
        }else{
            $reports = StakeholderReport::query()
                ->when($chapterIds->isNotEmpty(), fn ($q) => $q->whereIn('chapter_id', $chapterIds))
                ->when($zoneIds->isNotEmpty(), fn ($q) => $q->whereIn('zone_id', $zoneIds))
                ->when($fieldIds->isNotEmpty(), fn ($q) => $q->whereIn('field_id', $fieldIds));
        }
        // dd($reports->get(), $chapterIds, $zoneIds, $fieldIds);
        if ($request->filled('from_date')) {
            $reports->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $reports->whereDate('created_at', '<=', $request->to_date);
        }

        /** =====================
         * MANUAL FILTERS
         * ===================== */
        foreach (['chapter', 'zone', 'field'] as $scope) {
            if ($request->filled("{$scope}_filter")) {
                $reports->where("{$scope}_id", $request->input("{$scope}_filter"));
            }
        }

        /** =====================
         * STATUS FILTERS
         * ===================== */
        if ($request->filled('status_filter')) {
            $statusMap = [
                'field_pending'      => ['field_status', 0],
                'field_approved'     => ['field_status', 1],
                'field_rejected'     => ['field_status', 2],
                'zone_pending'       => ['zone_status', 0],
                'zone_approved'      => ['zone_status', 1],
                'zone_rejected'      => ['zone_status', 2],
                'national_pending'   => ['national_status', 0],
                'national_approved'  => ['national_status', 1],
                'national_rejected'  => ['national_status', 2],
            ];

            if (isset($statusMap[$request->status_filter])) {
                [$column, $value] = $statusMap[$request->status_filter];
                $reports->where($column, $value);
            }
        }

        if ($request->action === 'download') {
            $reportsCollection = $reports->get();

            return $this->downloadFinancialReport($reportsCollection);
        }

        return [
            'reports'  => $reports->with(['chapter', 'zone', 'field'])
                                  ->orderByDesc('created_at')
                                  ->paginate(20),

            'chapters' => $chaptersQuery->whereIn('id', $chapterIds)->orderBy('name')->get(),
            'zones'    => $zonesQuery->whereIn('id', $zoneIds)->orderBy('name')->get(),
            'fields'   => $fieldsQuery->whereIn('id', $fieldIds)->orderBy('name')->get(),
        ];
    }


    public function getScopedEntitiesForUser($user, $isAdmin = false)
    {
        $role = $user->role_id ?? $user->role;

        $chapterIds = collect();
        $zoneIds    = collect();
        $fieldIds   = collect();

        /** =====================
         * ROLE-BASED SCOPING
         * ===================== */
        if ($isAdmin || finStakeholders($user)) {
            $chapterIds = Chapter::pluck('id');
            $zoneIds    = Zone::pluck('id');
            $fieldIds   = Field::pluck('id');
        } else {
            if (in_array($role, chapterStakeholders())) {
                $chapterIds = collect([$user->chapter_id]);
                $zoneIds    = collect([$user->zone_id]);
                $fieldIds   = collect([$user->field_id]);
            } elseif (in_array($role, zoneStakeholders())) {
                $zoneIds = collect([$user->zone_id]);
                $chapterIds = Chapter::where('zone_id', $user->zone_id)->pluck('id');
                $fieldIds = Field::whereHas('zones', fn($q) => $q->where('id', $user->zone_id))->pluck('id');
            } elseif (in_array($role, fieldStakeholders())) {
                $fieldIds = collect([$user->field_id]);
                $zoneIds = Zone::where('field_id', $user->field_id)->pluck('id');
                $chapterIds = Chapter::whereIn('zone_id', $zoneIds)->pluck('id');
            } elseif (in_array($role, secretariatStakeholders())) {
                $chapterIds = Chapter::pluck('id');
                $zoneIds    = Zone::pluck('id');
                $fieldIds   = Field::pluck('id');
            }
        }

        return [
            'chapterIds' => $chapterIds,
            'zoneIds'    => $zoneIds,
            'fieldIds'   => $fieldIds,
            'chapters'   => Chapter::whereIn('id', $chapterIds)->orderBy('name')->get(),
            'zones'      => Zone::whereIn('id', $zoneIds)->orderBy('name')->get(),
            'fields'     => Field::whereIn('id', $fieldIds)->orderBy('name')->get(),
        ];
    }


    public function prepareEditData(StakeholderReport $report, $user, bool $isAdmin = false
    ): array
    {
        $report->load('answers');

        $months  = getMonths();
        $chapter = $user?->chapter;

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

        /** =====================
         * PREFILL (STATIC FIELDS)
         * ===================== */
        $prefillData = [
            'chapter_name'     => $chapter->name ?? '',
            'year_established' => $chapter->year_established ?? '',
            'president_name'   => '',
        ];

        /** =====================
         * ANSWERS (EDIT MODE)
         * ===================== */
        $answersData = [];

        foreach ($report->answers ?? [] as $answer) {
            $decoded = json_decode($answer->answer, true);
            $answersData[$answer->question_slug] =
                json_last_error() === JSON_ERROR_NONE
                    ? $decoded
                    : $answer->answer;
        }

        return compact(
            'months',
            'report',
            'sections',
            'prefillData',
            'answersData',
            'isAdmin'
        );
    }

    public function validateRequest(Request $request): array
    {
        return $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'nullable',
            'confirm_information' => 'accepted',
        ]);
    }

    public function saveReport($stakeholder, ?StakeholderReport $report, array $validated, bool $isAdmin = false
    ): array {
        DB::beginTransaction();

        try {

            /** =====================
             * ADMIN: SILENT UPDATE ONLY
             * ===================== */
            if ($isAdmin) {
                if (!$report) {
                    throw new \Exception('Report not found');
                }

                $this->saveResponses(
                    $stakeholder,
                    $report,
                    $validated['responses'],
                    $isAdmin
                );

                DB::commit();

                return [
                    'status'  => true,
                    'message' => 'Report updated successfully',
                ];
            }

            /** =====================
             * NORMAL FLOW (NON-ADMIN)
             * ===================== */
            $isNew    = false;
            $editMode = request()->boolean('edit_mode');

            if (!$report) {
                $report = new StakeholderReport();
                $isNew  = true;
            }

            /** =====================
             * ROLE-BASED OWNERSHIP
             * ===================== */
            if (in_array($stakeholder->role_id, chapterStakeholders(), true)) {

                $chapter = Chapter::with(['zone:id', 'field:id'])
                    ->find($stakeholder->chapter_id);

                if ($chapter && empty($chapter->year_established)) {
                    $chapter->update([
                        'year_established' =>
                            $validated['responses']['year_established'] ?? null
                    ]);
                }

                $report->chapter_id = (int) $stakeholder->chapter_id;
                $report->zone_id    = (int) (($chapter?->zone_id ?? $stakeholder->zone_id));
                $report->field_id   = (int) (($chapter?->field_id ?? $stakeholder->field_id));
            }

            /** =====================
             * CORE DATA
             * ===================== */

            $report->stakeholder_id = $stakeholder->id;
            $report->session        = $validated['responses']['session'] ?? $report->session;
            $report->year           = $validated['year'] ?? $report->year;
            $report->month          = $validated['month_number'] ?? $report->month;

            /** =====================
             * RESET REJECTIONS
             * ===================== */
            if (
                !$isNew &&
                in_array($stakeholder->role_id, chapterStakeholders(), true)
            ) {
                $this->resetRejectedStatuses($report);
            }

            if(!$isAdmin)
            {
                $report->edit_mode = $editMode ? 1 : 0;
            }

            $report->save();

            /** =====================
             * SAVE ANSWERS
             * ===================== */
            $this->saveResponses(
                $stakeholder,
                $report,
                $validated['responses'],
                $isAdmin
            );

            DB::commit();

            /** =====================
             * NOTIFICATIONS
             * ===================== */
            if (
                !$editMode &&
                in_array($stakeholder->role_id, chapterStakeholders(), true)
            ) {
                ReportNotificationService::handleReportSubmission(
                    $report->fresh(),
                    $stakeholder,
                    $isNew ? 'store' : 'update'
                );
            }

            return [
                'status'  => true,
                'message' => $isNew
                    ? 'Report submitted successfully'
                    : 'Report updated successfully',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            // dd($e->getFile(), $e->getLine(), );

            return [
                'status'  => false,
                'message' => 'Failed to save report: ' . $e->getMessage(),
            ];
        }
    }

    public function resetRejectedStatuses(StakeholderReport $report): void
    {
        $updates = [];

        if ($report->zone_status === 2) {
            $updates['zone_status'] = 0;
        }

        if ($report->field_status === 2) {
            $updates['field_status'] = 0;
        }

        if ($report->national_status === 2) {
            $updates['national_status'] = 0;
        }

        if ($updates) {
            $report->update($updates);
        }
    }

    public function saveResponses($stakeholder, StakeholderReport $report, array $responses, $isAdmin=false)
    {
        foreach ($responses as $slug => $answer) {
            $question = StakeholderReportQuestion::where('slug', $slug)->first();
            if (!$question) continue;

            $access = app('App\Services\StakeholderRolePermissionService')
                ->questionAccess($stakeholder, $question, $isAdmin);

            if (!$access['edit']) {
                continue;
            }


            $answerValue = is_array($answer) ? json_encode($answer) : $answer;
            $answerQuantity = null;
            $questionLabel = null;

            if($question->type == 'file' && request()->hasFile('responses.' . $question->slug)){
                $answerValue = app(FileUploadService::class)->secureUpload(
                request()->file('responses.' . $question->slug),
                'report-pops'
                );
            }

            if (in_array($question->type, ['select']) && !empty($question->options) && $question->is_quantifiable) {
                foreach ($question->options as $option) {
                    if (($option['value'] === $answerValue || $option['label'] === $answerValue)) {
                        $answerQuantity = $option['value'] ? (int) $option['value'] : null;
                        $questionLabel = $option['label'];
                        break;
                    }
                }
            }

            if (!empty($question->options) && !is_array($answer)) {
                foreach ($question->options as $option) {
                    if (($option['value'] === $answerValue || $option['label'] === $answerValue)) {
                        $questionLabel = $option['label'];
                        break;
                    }
                }
            }

            StakeholderReportAnswer::updateOrCreate(
                [
                    'report_id' => $report->id,
                    'question_id' => $question->id,
                ],
                [
                    'answer_value' => $answerValue,
                    'answer_quantity' => $answerQuantity,
                    'question_label' => $questionLabel,
                    'question_sub_section_id' => (int) $question->sub_section_id,
                    'question_section_id' => (int) $question->section_id,
                ]
            );

        }
    }

    public function canEditReport($report, $user)
    {
        $role = $user->role_id;

        $fieldStatus = $report->field_status;
        $zoneStatus  = $report->zone_status;
        $natStatus   = $report->national_status;

        // Fully approved
        $allApproved = $fieldStatus == 1 && $zoneStatus == 1 && $natStatus == 1;

        /*
        |--------------------------------------------------------------------------
        | NATIONAL APPROVAL
        |--------------------------------------------------------------------------
        | Locks everyone except Super Admin
        */
        if ($natStatus == 1) {
            return [
                'allApproved' => true,
                'canEdit' => $role == 1, // Super Admin only
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | FIELD APPROVAL
        |--------------------------------------------------------------------------
        | Locks Field, Zone, Chapter
        */
        if ($fieldStatus == 1) {
            return [
                'allApproved' => false,
                'canEdit' => (
                    in_array($role, secretariatStakeholders(), true) ||
                    in_array($role, ncpStakeholders(), true) ||
                    $role == 1
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | ZONE APPROVAL
        |--------------------------------------------------------------------------
        | Locks Zone & Chapter
        */
        if ($zoneStatus == 1) {
            return [
                'allApproved' => false,
                'canEdit' => !(
                    in_array($role, zoneStakeholders(), true) ||
                    in_array($role, chapterStakeholders(), true)
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | NO LOCKS
        |--------------------------------------------------------------------------
        */
        return [
            'allApproved' => false,
            'canEdit' => true,
        ];
    }


    public function prepareViewData(StakeholderReport $report, bool $isAdmin = false
    ): array
    {
        $report->load('answers');

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

        $answersData = [];

        foreach ($report->answers ?? [] as $answer) {
            $decoded = json_decode($answer->answer_value, true);
            $answersData[$answer->question->label] =
                json_last_error() === JSON_ERROR_NONE
                    ? $decoded
                    : $answer->answer_value;
        }

        return compact(
            'report',
            'sections',
            'answersData',
            'isAdmin'
        );
    }

    public function deleteReport(StakeholderReport $report)
    {
        DB::beginTransaction();

        try {
            // Delete associated answers
            StakeholderReportAnswer::where('report_id', $report->id)->delete();

            // Delete the report itself
            $report->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e; // Re-throw the exception after rolling back
        }
    }

    public function downloadFinancialReport($reports, ?array $headers = [])
    {
        $fileName = 'financial-report-'.now().'.xlsx';
        $isAdmin = false;

        /**
         * Weekly JSON columns to sum
         * Excel Header => JSON Key
         */
        $sumColumns = [
            'Total Sunday Worship Offering' => 'Sunday Worship Offering',
            'Total Weekly Tithe Received (Bank)' => 'Tithe received(Bank)',
            'Total Weekly Tithe Received (Cash)' => 'Tithe received(Cash)',
            'Total Weekly Bible Study Offering'  => 'Bible Study Offering',
            'Total Weekly Prayer Meeting Offering' => 'Prayer Meeting Offering',
            'Total Weekly Other Offering'        => 'Other Offering',
        ];

        /** -------------------------
         * QUESTIONS (ID → LABEL MAP)
         * ------------------------- */
        $questions = StakeholderReportQuestion::whereHas('subsection', function ($q) {
                $q->whereIn('id', finSubSectionIds());
            })
            ->select('id', 'label')
            ->get()
            ->reject(fn ($q) => in_array($q->label, ['Income Records', 'Expenditure Records']));

        $questionMap = $questions->pluck('label', 'id')->toArray();

        /** -------------------------
         * HEADERS
         * ------------------------- */
        if (empty($headers)) {
            $headers = array_merge(
                ['Chapter Name', 'Date Updated'],
                array_keys($sumColumns),
                array_values($questionMap)
            );
        }

        $rows = [];
        if(empty($reports)){
            return null;
        }

        foreach ($reports as $report) {
            // Initialize row with nulls
            $row = array_fill_keys($headers, null);

            /** -------------------------
             * STATIC COLUMNS
             * ------------------------- */
            $row['Chapter Name'] = $report->chapter?->name;
            $row['Date Updated'] = optional($report->updated_at)->format('Y-m-d');

            /** -------------------------
             * INIT SUM COLUMNS
             * ------------------------- */
            foreach (array_keys($sumColumns) as $header) {
                $row[$header] = 0;
            }

            /** -------------------------
             * ANSWERS
             * ------------------------- */
            foreach ($report->answers as $answer) {
                $decoded = json_decode($answer->answer_value, true);

                // CASE 1: Weekly JSON
                if (is_array($decoded)) {
                    foreach ($decoded as $weekData) {
                        if (!is_array($weekData)) continue;

                        foreach ($sumColumns as $header => $jsonKey) {
                            if (isset($weekData[$jsonKey])) {
                                $row[$header] += (float) $weekData[$jsonKey];
                            }
                        }
                    }
                    continue;
                }

                // CASE 2: Scalar value mapped by question_id
                if (!isset($questionMap[$answer->question_id])) continue;

                $column = $questionMap[$answer->question_id];

                // CASE 3: File type → generate protected download link
                if ($answer->question?->type === 'file' && !empty($answer->answer_value)) {
                    $row[$column] = route(
                        $isAdmin ? 'admin.protected.download' : 'protected.download',
                        ['file' => $answer->answer_value]
                    );
                    continue;
                }

                // CASE 4: Normal scalar
                $row[$column] = is_numeric($answer->answer_value)
                    ? (float) $answer->answer_value
                    : $answer->answer_value;
            }

            $rows[] = $row;
        }

        return ExcelService::download($rows, $headers, $fileName);
    }


    public function approve($user, $report){
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

        return;
    }

    public function reject($user, $report){
        $comment = request()->rejection_reason;

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
                $report->zone_status = 0;
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
                $report->zone_status = 0;
                $report->field_status = 0;

                break;

            default:
                abort(403, 'Unauthorized action');
        }

        $report->save();

        ReportNotificationService::handleReportAction($report, $user, 'reject');

        return;
    }

    public function nudgeReportActors($stakeholder, $report){
        ReportNotificationService::handleReportAction(
            $report,
            $stakeholder,
            'nudge'
        );

        return;
    }
}
