<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\ConferenceEdition;
use App\Models\StakeholderReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\StakeholderReportAnswer;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;

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

        return $sessions;
    }

    public function index(Request $request, $user, bool $isAdmin = false): array
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
        if ($isAdmin) {
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
        $reports = StakeholderReport::query()
            ->when($chapterIds->isNotEmpty(), fn ($q) => $q->whereIn('chapter_id', $chapterIds))
            ->when($zoneIds->isNotEmpty(), fn ($q) => $q->whereIn('zone_id', $zoneIds))
            ->when($fieldIds->isNotEmpty(), fn ($q) => $q->whereIn('field_id', $fieldIds));

        /** =====================
         * DATE FILTERS
         * ===================== */
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

        /** =====================
         * EXECUTE
         * ===================== */
        return [
            'reports'  => $reports->with(['chapter', 'zone', 'field'])
                                  ->orderByDesc('created_at')
                                  ->paginate(20),

            'chapters' => $chaptersQuery->whereIn('id', $chapterIds)->orderBy('name')->get(),
            'zones'    => $zonesQuery->whereIn('id', $zoneIds)->orderBy('name')->get(),
            'fields'   => $fieldsQuery->whereIn('id', $fieldIds)->orderBy('name')->get(),
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

                $report->chapter_id = $stakeholder->chapter_id;
                $report->zone_id    = $stakeholder->zone_id  ?? $chapter?->zone?->id;
                $report->field_id   = $stakeholder->field_id ?? $chapter?->field?->id;
            }

            /** =====================
             * CORE DATA
             * ===================== */
            $report->stakeholder_id = $stakeholder->id;
            $report->session        = $validated['responses']['session'] ?? $report->session;
            $report->year           = $validated['responses']['year'] ?? $report->year;
            $report->month          = $validated['responses']['month'] ?? $report->month;

            /** =====================
             * RESET REJECTIONS
             * ===================== */
            if (
                !$isNew &&
                in_array($stakeholder->role_id, chapterStakeholders(), true)
            ) {
                $this->resetRejectedStatuses($report);
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
                ]
            );
        }
    }

    public function checks($stakeholder){

        $data = [
            'status' => true,
            'message' => 'success'
        ];

        if (
            is_null($stakeholder->signature) ||
            is_null($stakeholder->gen_sec_signature) ||
            is_null($stakeholder->fin_sec_signature) ||
            is_null($stakeholder->evang_sec_signature)
        ) {
            $data = [
                'status' => false,
                'message' => 'Kindly upload signatures first, you will only need to do this once'
            ];
        }

        return $data;
    }

    public function canEditReport($report, $user){
        $fieldStatus = $report->field_status;
        $zoneStatus = $report->zone_status;
        $natStatus  = $report->national_status;

        $userRole = $user->role_id;
        // Determine if the report is fully approved
        $allApproved = $zoneStatus == 1 && $fieldStatus == 1 && $natStatus == 1;

        // Determine if edit is allowed
        $canEdit = (
            (in_array($userRole, fieldStakeholders()) && $fieldStatus == 0) ||
            (in_array($userRole, zoneStakeholders()) && $zoneStatus == 0) ||
            (in_array($userRole, chapterStakeholders()) && $zoneStatus == 0) ||
            in_array($userRole, secretariatStakeholders()) ||
            in_array($userRole, ncpStakeholders()) ||
            in_array($userRole, [1])
        );

        return [
            'allApproved' => $allApproved,
            'canEdit'      => $canEdit
        ];
    }

}
