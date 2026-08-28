<?php

namespace App\Services;

use App\Models\Stakeholder;
use App\Models\StakeholderAppraisal;
use App\Models\StakeholderAppraisalAnswer;
use App\Models\StakeholderDesignation;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderRole;
use App\Services\EmailService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AppraisalService
{
    public const AUDIENCE_FILL = 'fill';
    public const AUDIENCE_EVALUATE = 'evaluate';

    public function dashboardAccess(Stakeholder $user): array
    {
        $permissionProfile = $this->appraisalPermissionProfile($user);

        $hasAppraisalSystemAccess = (bool) ($user->access_appraisal_system ?? false);
        $hasAppraisalEvaluationAccess = (bool) ($user->access_appraisal_evaluation ?? false);
        $isNationalPresident = $this->isNationalPresident($user);

        if ($isNationalPresident) {
            $canSelfAppraise = $hasAppraisalSystemAccess;
            $canEvaluate = $hasAppraisalEvaluationAccess;
        } else {
            $canSelfAppraise = $hasAppraisalSystemAccess && $this->hasAnyAppraisalPermission($user, $permissionProfile['fill'] ?? []);
            $canEvaluate = $hasAppraisalEvaluationAccess && $this->hasAnyAppraisalPermission($user, $permissionProfile['evaluate'] ?? []);
        }

        return [
            'my_appraisal' => $canSelfAppraise,
            'evaluations' => $canEvaluate,
            self::AUDIENCE_FILL => $canSelfAppraise,
            self::AUDIENCE_EVALUATE => $canEvaluate,
            'national_president' => $isNationalPresident,
        ];
    }

    public function hasAppraisalPermission(Stakeholder $user, string $permission): bool
    {
        $candidates = $this->appraisalPermissionAliases($permission);

        foreach ($candidates as $candidate) {
            if ($user->hasPermission($candidate)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyAppraisalPermission(Stakeholder $user, array|string|null $permissions): bool
    {
        $permissionList = is_array($permissions) ? $permissions : array_filter([$permissions]);

        if (empty($permissionList)) {
            return false;
        }

        foreach ($permissionList as $permission) {
            if ($this->hasAppraisalPermission($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasMenuAccess(Stakeholder $user): bool
    {
        $access = $this->dashboardAccess($user);

        return (bool) (
            ($access[self::AUDIENCE_FILL] ?? false)
            || ($access[self::AUDIENCE_EVALUATE] ?? false)
        );
    }

    public function canAccessMode(Stakeholder $user, string $mode): bool
    {
        $access = $this->dashboardAccess($user);

        return (bool) ($access[$mode] ?? false);
    }

    public function structure(Stakeholder $user, array $audiences, bool $isAdmin = false, ?string $formPrefix = null): Collection
    {
        $permissionService = app(StakeholderRolePermissionService::class);

        $sections = StakeholderQuestionSection::forModule('appraisal')
            ->isActive()
            ->with([
                'subsections' => function ($subQuery) {
                    $subQuery->isActive()->with([
                        'questions' => function ($questionQuery) {
                            $questionQuery->isActive()->orderBy('order');
                        },
                    ])->orderBy('id');
                },
            ])
            ->orderBy('id')
            ->get();

        return $sections->map(function ($section) use ($permissionService, $user, $audiences, $isAdmin, $formPrefix) {
            if ($formPrefix && ! Str::startsWith($section->slug ?? '', $formPrefix . '-')) {
                return null;
            }

            if (! $isAdmin && ! $permissionService->sectionAccess($user, $section)['view']) {
                return null;
            }

            $subsections = $section->subsections
                ->map(function ($subsection) use ($permissionService, $user, $audiences, $isAdmin) {
                    if (! $isAdmin && ! $permissionService->sectionAccess($user, $subsection)['view']) {
                        return null;
                    }

                    $questions = $subsection->questions
                        ->filter(function ($question) use ($permissionService, $user, $audiences, $isAdmin) {
                            if (! in_array($question->audience ?? self::AUDIENCE_FILL, $audiences, true)) {
                                return false;
                            }

                            return $permissionService->questionAccess($user, $question, $isAdmin)['view'];
                        })
                        ->values();

                    $subsection->setRelation('questions', $questions);

                    return $questions->isNotEmpty() ? $subsection : null;
                })
                ->filter()
                ->values();

            $section->setRelation('subsections', $subsections);

            return $subsections->isNotEmpty() ? $section : null;
        })->filter()->values();
    }

    public function structureForMode(Stakeholder $user, string $mode, bool $isAdmin = false, ?string $formPrefix = null): Collection
    {
        $formPrefix = $formPrefix ?: $this->appraisalFormPrefix($user);

        return match ($mode) {
            'my', self::AUDIENCE_FILL => $this->structure($user, [self::AUDIENCE_FILL], $isAdmin, $formPrefix),
            'evaluations', self::AUDIENCE_EVALUATE => $this->structure(
                $user,
                $this->evaluationAudiencesFor($user, $isAdmin),
                $isAdmin,
                $formPrefix
            ),
            default => collect(),
        };
    }

    protected function evaluationAudiencesFor(Stakeholder $user, bool $isAdmin = false): array
    {
        $audiences = [self::AUDIENCE_EVALUATE];

        if ($isAdmin || $this->isNationalPresident($user)) {
            $audiences[] = 'national_president';
        }

        return array_values(array_unique($audiences));
    }

    public function summary(Stakeholder $user): array
    {
        $mySections = $this->structureForMode($user, 'my');
        $evaluationSections = $this->structureForMode($user, 'evaluations');

        return [
            'sections' => $mySections->count() + $evaluationSections->count(),
            'subsections' => $mySections->sum(fn ($section) => $section->subsections->count())
                + $evaluationSections->sum(fn ($section) => $section->subsections->count()),
            'questions' => $mySections->sum(fn ($section) => $section->subsections->sum(fn ($subsection) => $subsection->questions->count()))
                + $evaluationSections->sum(fn ($section) => $section->subsections->sum(fn ($subsection) => $subsection->questions->count())),
            'my_sections' => $mySections->count(),
            'my_subsections' => $mySections->sum(fn ($section) => $section->subsections->count()),
            'my_questions' => $mySections->sum(fn ($section) => $section->subsections->sum(fn ($subsection) => $subsection->questions->count())),
            'evaluation_sections' => $evaluationSections->count(),
            'evaluation_subsections' => $evaluationSections->sum(fn ($section) => $section->subsections->count()),
            'evaluation_questions' => $evaluationSections->sum(fn ($section) => $section->subsections->sum(fn ($subsection) => $subsection->questions->count())),
        ];
    }

    public function getOrCreateSelfAppraisal(Stakeholder $appraisee): StakeholderAppraisal
    {
        return StakeholderAppraisal::firstOrCreate(
            ['appraisee_id' => $appraisee->id],
            ['self_status' => 'draft', 'evaluation_status' => 'draft']
        );
    }

    public function selfAppraisalFor(Stakeholder $appraisee): StakeholderAppraisal
    {
        return $this->getOrCreateSelfAppraisal($appraisee)->fresh(['answers', 'appraisee', 'evaluator']);
    }

    public function selfAppraisalPrefillData(Stakeholder $user): array
    {
        $period = 'May 2025 – ' . date('F Y');
        $fieldName = $user->field?->name;
        $zoneName = $user->zone?->name;
        $chapterName = $user->chapter?->name;
        $officeName = $user->designation?->name ?: ($user->role?->name ?? '');
        $scopeName = $fieldName ?: $zoneName ?: $chapterName ?: $officeName;
        $formPrefix = $this->appraisalFormPrefix($user);

        $defaults = [];

        if ($formPrefix) {
            $defaults["{$formPrefix}-period-under-review"] = $period;
            $defaults["{$formPrefix}-office-position-held"] = $officeName;
        }

        if ($user->name && $formPrefix) {
            $defaults["{$formPrefix}-name-of-field-pastor"] = $user->name;
            $defaults["{$formPrefix}-name-of-zonal-pastor"] = $user->name;
            $defaults["{$formPrefix}-name-of-national-officer"] = $user->name;
            $defaults["{$formPrefix}-name-of-national-president"] = $user->name;
        }

        if ($formPrefix === 'field-pastor') {
            $defaults['field-pastor-field-area-covered'] = $fieldName ?: $scopeName;
            $defaults['field-pastor-number-of-zones-under-supervision'] = $user->field?->zones()->count() ?? 0;
        }

        if ($formPrefix === 'zonal-pastor') {
            $defaults['zonal-pastor-field-area-covered'] = $zoneName ?: $scopeName;
            $defaults['zonal-pastor-zone'] = $zoneName ?: '';
            $defaults['zonal-pastor-number-of-campuses'] = $user->zone?->chapters()->count() ?? 0;
        }

        if ($formPrefix === 'national-officer') {
            $defaults['national-officer-major-responsibilities-of-the-office'] = $officeName;
        }

        if ($formPrefix === 'national-president') {
            $defaults['national-president-major-responsibilities-of-the-office'] = $officeName;
        }

        return array_filter($defaults, fn ($value) => $value !== null && $value !== '');
    }

    public function appraisalFormPrefix(Stakeholder $user): ?string
    {
        $designationName = $user?->designation?->name ?? '';
        $roleSlug = $user?->role?->slug;

        if ($designationName === 'National President') {
            return 'national-president';
        }

        if ($roleSlug === 'field-pastor') {
            return 'field-pastor';
        }

        if ($roleSlug === 'zonal-pastor' || str_contains($designationName, 'Zonal Pastor')) {
            return 'zonal-pastor';
        }

        if ($roleSlug === 'nec-member' || $roleSlug === 'nec' || str_contains($designationName, 'National Officer')) {
            return 'national-officer';
        }

        return null;
    }

    public function loadAnswersForAudience(StakeholderAppraisal $appraisal, string $audience): Collection
    {
        return $appraisal->answers()
            ->where('audience', $audience)
            ->get()
            ->keyBy('question_slug');
    }

    public function loadSelfAnswers(StakeholderAppraisal $appraisal): Collection
    {
        return $this->loadAnswersForAudience($appraisal, self::AUDIENCE_FILL);
    }

    public function loadEvaluationAnswers(StakeholderAppraisal $appraisal): Collection
    {
        return $appraisal->answers()
            ->where('audience', self::AUDIENCE_EVALUATE)
            ->get()
            ->keyBy(fn ($answer) => $answer->audience . ':' . $answer->question_slug);
    }

    public function evaluationTargets(Stakeholder $user): Collection
    {
        if ($this->isNationalPresident($user)) {
            return $this->necMembers()
                ->reject(fn (Stakeholder $stakeholder) => $stakeholder->id === $user->id)
                ->values();
        }

        if ($user?->role?->slug === 'ncp') {
            $fieldPastorRoleIds = $this->fieldPastorRoleIds();

            return Stakeholder::query()
                ->with('designation', 'role')
                ->where('id', '!=', $user->id)
                ->where(function ($query) use ($fieldPastorRoleIds) {
                    $query->whereIn('role_id', $fieldPastorRoleIds)
                        ->orWhere('designation_id', $this->nationalPresidentDesignationId());
                })
                ->get()
                ->values();
        }

        if ($user?->role?->slug === 'field-pastor') {
            $fieldId = $user->field_id;

            if (! $fieldId) {
                return collect();
            }

            return Stakeholder::query()
                ->with('designation', 'role')
                ->where('field_id', $fieldId)
                ->whereIn('role_id', $this->zonalPastorRoleIds())
                ->where('id', '!=', $user->id)
                ->get()
                ->filter(function (Stakeholder $stakeholder) {
                    $designationName = $stakeholder->designation?->name ?? '';

                    return str_contains($designationName, 'Zonal Pastor')
                        || str_contains($designationName, 'Assistant Zonal Pastor');
                })
                ->values();
        }

        return collect();
    }

    public function evaluationCandidates(Stakeholder $user): Collection
    {
        return $this->evaluationTargets($user)
            ->map(function (Stakeholder $target) {
                $appraisal = $this->appraisalForEvaluation($target);

                if (! $appraisal) {
                    return null;
                }

                $target->setRelation('published_appraisal', $appraisal);

                return $target;
            })
            ->filter()
            ->values();
    }

    public function evaluatorAudience(Stakeholder $evaluator, Stakeholder $target): string
    {
        if ($this->isNationalPresident($evaluator)) {
            return $this->isNationalPresident($target)
                ? self::AUDIENCE_EVALUATE
                : 'national_president';
        }

        return self::AUDIENCE_EVALUATE;
    }

    public function evaluationAuthorityLabel(Stakeholder $target): string
    {
        $designationName = $target?->designation?->name ?? '';
        $roleSlug = $target?->role?->slug;

        if ($designationName === 'National President') {
            return 'NCP';
        }

        if ($roleSlug === 'nec-member' || $roleSlug === 'nec' || str_contains($designationName, 'National Officer')) {
            return 'National President';
        }

        if ($roleSlug === 'zonal-pastor' || str_contains($designationName, 'Zonal Pastor')) {
            return 'Field Pastor';
        }

        if ($roleSlug === 'field-pastor') {
            return 'NCP';
        }

        return 'Evaluator';
    }

    public function evaluationPrefillData(Stakeholder $evaluator, Stakeholder $target): array
    {
        $formPrefix = $this->appraisalFormPrefix($target) ?: $this->appraisalFormPrefix($evaluator);
        $position = $evaluator->designation?->name ?: ($evaluator->role?->name ?? '');

        if (! $formPrefix) {
            return [];
        }

        return array_filter([
            "{$formPrefix}-appraiser-name" => $evaluator->name ?? '',
            "{$formPrefix}-appraiser-position" => $position,
            "{$formPrefix}-appraiser-date" => now()->format('Y-m-d'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    public function canViewPublishedSelfAppraisal(Stakeholder $evaluator, StakeholderAppraisal $appraisal): bool
    {
        return $appraisal->self_status === 'published'
            && in_array($appraisal->appraisee->id, $this->evaluationTargets($evaluator)->pluck('id')->all(), true);
    }

    public function saveSelfAppraisal(Stakeholder $appraisee, array $payload, string $status): StakeholderAppraisal
    {
        return DB::transaction(function () use ($appraisee, $payload, $status) {
            $appraisal = $this->getOrCreateSelfAppraisal($appraisee);
            $appraisal->update([
                'self_status' => $status,
                'self_published_at' => $status === 'published' ? now() : $appraisal->self_published_at,
            ]);

            $this->syncAnswers($appraisal, $payload, self::AUDIENCE_FILL, $appraisee->id);

            return $appraisal->fresh(['answers', 'appraisee', 'evaluator']);
        });
    }

    public function saveEvaluation(Stakeholder $evaluator, Stakeholder $target, array $payload, string $status): StakeholderAppraisal
    {
        return DB::transaction(function () use ($evaluator, $target, $payload, $status) {
            $appraisal = $this->getOrCreateSelfAppraisal($target);
            $appraisal->update([
                'evaluator_id' => $evaluator->id,
                'evaluation_status' => $status,
                'evaluation_published_at' => $status === 'published' ? now() : $appraisal->evaluation_published_at,
            ]);

            $this->syncAnswers(
                $appraisal,
                $payload,
                $this->evaluatorAudience($evaluator, $target),
                $evaluator->id
            );

            return $appraisal->fresh(['answers', 'appraisee', 'evaluator']);
        });
    }

    public function unlockSelfAppraisal(StakeholderAppraisal $appraisal): StakeholderAppraisal
    {
        $appraisal->update([
            'self_status' => 'draft',
            'self_published_at' => null,
        ]);

        return $appraisal->fresh(['answers', 'appraisee', 'evaluator']);
    }

    public function unlockEvaluation(StakeholderAppraisal $appraisal): StakeholderAppraisal
    {
        $appraisal->update([
            'evaluation_status' => 'draft',
            'evaluation_published_at' => null,
        ]);

        return $appraisal->fresh(['answers', 'appraisee', 'evaluator']);
    }

    public function prepareSubmissionAnswers(
        Stakeholder $user,
        Request $request,
        string $mode,
        string $status,
        ?StakeholderAppraisal $appraisal = null,
        ?Stakeholder $target = null
    ): array {
        $formPrefix = $mode === 'my'
            ? $this->appraisalFormPrefix($user)
            : $this->appraisalFormPrefix($target ?? $user);

        $sections = $this->structureForMode($user, $mode, false, $formPrefix);
        $audience = $mode === 'my'
            ? self::AUDIENCE_FILL
            : $this->evaluatorAudience($user, $target ?? $user);

        $existingAnswers = $appraisal
            ? $this->loadAnswersForAudience($appraisal, $audience)
            : collect();

        $submittedAnswers = $request->input('answers', []);
        $uploadedAnswers = $request->file('answers', []);
        $normalizedAnswers = [];
        $errors = [];

        foreach ($sections as $section) {
            foreach ($section->subsections as $subsection) {
                foreach ($subsection->questions as $question) {
                    $slug = $question->slug;
                    $required = ! empty($question->is_required);
                    $existingValue = $existingAnswers->get($slug)?->answer_value;
                    $uploadedFile = data_get($uploadedAnswers, $slug);
                    $incomingValue = $submittedAnswers[$slug] ?? null;
                    $fieldKey = "answers.{$slug}";

                    if (($question->type ?? '') === 'file') {
                        if ($uploadedFile instanceof UploadedFile) {
                            if (! $uploadedFile->isValid()) {
                                $errors[$fieldKey] = "{$question->label} upload is invalid.";
                                continue;
                            }

                            $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: '');
                            $allowedExtensions = ['jpg', 'jpeg', 'png'];

                            if (! in_array($extension, $allowedExtensions, true)) {
                                $errors[$fieldKey] = "{$question->label} must be a JPG, JPEG, or PNG file.";
                                continue;
                            }

                            $normalizedAnswers[$slug] = app(FileUploadService::class)->secureUpload(
                                $uploadedFile,
                                'appraisal-signatures',
                                $existingValue ?: null
                            );

                            continue;
                        }

                        if (! empty($existingValue)) {
                            $normalizedAnswers[$slug] = $existingValue;
                            continue;
                        }

                        if ($status === 'published' && $required) {
                            $errors[$fieldKey] = "{$question->label} is required for final submission.";
                        }

                        continue;
                    }

                    if (is_array($incomingValue)) {
                        $isEmpty = empty(array_filter($incomingValue, fn ($value) => $value !== null && $value !== ''));
                    } else {
                        $isEmpty = $incomingValue === null || $incomingValue === '';
                    }

                    if ($status === 'published' && $required && $isEmpty) {
                        $errors[$fieldKey] = "{$question->label} is required for final submission.";
                        continue;
                    }

                    $normalizedAnswers[$slug] = $incomingValue;
                }
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $normalizedAnswers;
    }

    public function appraisalForEvaluation(Stakeholder $target): ?StakeholderAppraisal
    {
        return StakeholderAppraisal::with(['answers', 'appraisee', 'evaluator'])
            ->where('appraisee_id', $target->id)
            ->where('self_status', 'published')
            ->first();
    }

    public function appraisalRecordForPdf(Stakeholder $target): ?StakeholderAppraisal
    {
        return StakeholderAppraisal::with(['answers', 'appraisee', 'evaluator'])
            ->where('appraisee_id', $target->id)
            ->first();
    }

    public function appraisalPdfData(Stakeholder $target, ?Stakeholder $viewer = null, bool $isAdmin = false): array
    {
        $appraisal = $this->appraisalRecordForPdf($target);
        $formPrefix = $this->appraisalFormPrefix($target);
        $evaluator = $appraisal?->evaluator ?: ($viewer ?: $target);
        $audience = $appraisal
            ? $this->evaluatorAudience($evaluator, $target)
            : self::AUDIENCE_EVALUATE;

        return [
            'target' => $target->loadMissing(['designation', 'role', 'field', 'zone', 'chapter']),
            'appraisal' => $appraisal,
            'selfSections' => $this->structureForMode($target, 'my', $isAdmin, $formPrefix),
            'evaluationSections' => $this->structureForMode($evaluator, 'evaluations', $isAdmin, $formPrefix),
            'selfAnswers' => $appraisal ? $this->loadSelfAnswers($appraisal) : collect(),
            'evaluationAnswers' => $appraisal ? $this->loadAnswersForAudience($appraisal, $audience) : collect(),
            'audience' => $audience,
            'evaluationAuthorityLabel' => $this->evaluationAuthorityLabel($target),
            'selfStatus' => $appraisal?->self_status ?? 'draft',
            'evaluationStatus' => $appraisal?->evaluation_status ?? 'draft',
            'selfPublishedAt' => $appraisal?->self_published_at,
            'evaluationPublishedAt' => $appraisal?->evaluation_published_at,
            'formPrefix' => $formPrefix,
            'instructionProfile' => appraisalInstructionProfile($formPrefix),
        ];
    }

    public function appraisalExportSheets(Collection $stakeholders): array
    {
        $questions = $this->appraisalExportQuestions();
        $questionHeaders = $questions->map(fn ($question) => $question['header'])->all();

        $responseHeaders = array_merge([
            'S/N',
            'Stakeholder',
            'Role',
            'Designation',
            'Field',
            'Zone',
            'Self Status',
            'Evaluation Status',
            'Self Published At',
            'Evaluation Published At',
        ], $questionHeaders);

        $responsesRows = [];
        $ratingsRows = [];
        $responseIndex = 1;
        $ratingIndex = 1;

        foreach ($stakeholders as $stakeholder) {
            $appraisal = $stakeholder->appraisal;
            $answers = ($appraisal?->answers ?? collect())->keyBy(fn ($answer) => $answer->audience . ':' . $answer->question_slug);
            $selfScores = [];
            $evaluationScores = [];

            $row = [
                'S/N' => $responseIndex++,
                'Stakeholder' => $stakeholder->name,
                'Role' => $stakeholder->role?->name ?? '',
                'Designation' => $stakeholder->designation?->name ?? '',
                'Field' => $stakeholder->field?->name ?? '',
                'Zone' => $stakeholder->zone?->name ?? '',
                'Self Status' => $appraisal?->self_status ?? 'draft',
                'Evaluation Status' => $appraisal?->evaluation_status ?? 'draft',
                'Self Published At' => optional($appraisal?->self_published_at)->format('d M Y, h:i A'),
                'Evaluation Published At' => optional($appraisal?->evaluation_published_at)->format('d M Y, h:i A'),
            ];

            foreach ($questions as $question) {
                $questionSlug = $question['slug'];
                $questionModel = $question['model'];
                $answer = $answers->get(self::AUDIENCE_FILL . ':' . $questionSlug);
                $answerValue = $answer?->answer_value;

                $row[$question['header']] = $this->appraisalExportAnswerText($questionModel, $answerValue);

                $numericScore = $this->appraisalExportNumericScore($questionModel, $answerValue);
                if ($numericScore !== null) {
                    $selfScores[] = $numericScore;
                }
            }

            $responsesRows[] = $row;

            foreach ($answers->filter(fn ($answer) => $answer->audience !== self::AUDIENCE_FILL) as $answer) {
                $questionEntry = $questions->firstWhere('slug', $answer->question_slug);
                $questionModel = $questionEntry['model'] ?? null;
                $numericScore = $this->appraisalExportNumericScore($questionModel, $answer->answer_value);

                if ($numericScore !== null) {
                    $evaluationScores[] = $numericScore;
                }
            }

            $allScores = array_values(array_filter($selfScores, fn ($score) => $score !== null));

            $ratingsRows[] = [
                'S/N' => $ratingIndex++,
                'Stakeholder' => $stakeholder->name,
                'Role' => $stakeholder->role?->name ?? '',
                'Designation' => $stakeholder->designation?->name ?? '',
                'Field' => $stakeholder->field?->name ?? '',
                'Zone' => $stakeholder->zone?->name ?? '',
                'Self Questions Scored' => count($selfScores),
                'Self Average' => $this->appraisalAverageScore($selfScores),
                'Self Rating' => $this->appraisalRatingLabel($selfScores),
                'Evaluation Questions Scored' => count($evaluationScores),
                'Evaluation Average' => $this->appraisalAverageScore($evaluationScores),
                'Evaluation Rating' => $this->appraisalRatingLabel($evaluationScores),
                'Overall Questions Scored' => count(array_filter(array_merge($selfScores, $evaluationScores), fn ($score) => $score !== null)),
                'Overall Average' => $this->appraisalAverageScore(array_merge($selfScores, $evaluationScores)),
                'Overall Rating' => $this->appraisalRatingLabel(array_merge($selfScores, $evaluationScores)),
                'Appraiser' => $appraisal?->evaluator?->name ?? '',
                'Self Status' => $appraisal?->self_status ?? 'draft',
                'Evaluation Status' => $appraisal?->evaluation_status ?? 'draft',
            ];
        }

        return [
            'Responses' => [
                'headers' => $responseHeaders,
                'rows' => $responsesRows,
            ],
            'Ratings' => [
                'headers' => array_keys($ratingsRows[0] ?? [
                    'S/N' => '',
                    'Stakeholder' => '',
                    'Role' => '',
                    'Designation' => '',
                    'Field' => '',
                    'Zone' => '',
                    'Self Questions Scored' => '',
                    'Self Average' => '',
                    'Self Rating' => '',
                    'Evaluation Questions Scored' => '',
                    'Evaluation Average' => '',
                    'Evaluation Rating' => '',
                    'Overall Questions Scored' => '',
                    'Overall Average' => '',
                    'Overall Rating' => '',
                    'Appraiser' => '',
                    'Self Status' => '',
                    'Evaluation Status' => '',
                ]),
                'rows' => $ratingsRows,
            ],
        ];
    }

    protected function appraisalExportQuestions(): Collection
    {
        return StakeholderReportQuestion::query()
            ->where('module_type', 'appraisal')
            ->with(['section', 'subsection'])
            ->get()
            ->sortBy(function (StakeholderReportQuestion $question) {
                return sprintf(
                    '%03d|%03d|%03d|%s',
                    (int) ($question->section?->order ?? 0),
                    (int) ($question->subsection?->order ?? 0),
                    (int) ($question->order ?? 0),
                    $question->label ?? ''
                );
            })
            ->values()
            ->map(function (StakeholderReportQuestion $question) {
                return [
                    'slug' => $question->slug,
                    'header' => trim(($question->section?->name ? $question->section->name . ' / ' : '') . ($question->label ?? $question->slug)),
                    'model' => $question,
                ];
            });
    }

    protected function appraisalExportAnswerText(?StakeholderReportQuestion $question, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        $decoded = $this->appraisalMaybeDecodeJson($value);
        $options = is_array($question?->options ?? null) ? $question->options : [];

        $findLabel = function ($needle) use ($options) {
            foreach ($options as $option) {
                $optionValue = $option['value'] ?? null;
                $optionLabel = $option['label'] ?? $optionValue;

                if ((string) $optionValue === (string) $needle) {
                    return $optionLabel;
                }
            }

            return null;
        };

        if (is_array($decoded)) {
            $mapped = array_map(function ($item) use ($findLabel) {
                return $findLabel($item) ?? $item;
            }, $decoded);

            return implode(', ', array_filter($mapped, fn ($item) => $item !== null && $item !== ''));
        }

        if ($question && ($question->type ?? 'text') === 'file') {
            $decodedPath = is_string($decoded) ? base64_decode($decoded, true) : null;

            return basename($decodedPath ?: (string) $decoded ?: (string) $value);
        }

        return $findLabel($decoded) ?? (string) $decoded;
    }

    protected function appraisalExportNumericScore(?StakeholderReportQuestion $question, mixed $value): ?float
    {
        $decoded = $this->appraisalMaybeDecodeJson($value);

        if (is_array($decoded)) {
            return null;
        }

        if (is_numeric($decoded)) {
            return (float) $decoded;
        }

        $options = is_array($question?->options ?? null) ? $question->options : [];

        foreach ($options as $option) {
            $optionValue = $option['value'] ?? null;

            if ((string) $optionValue === (string) $decoded && is_numeric($optionValue)) {
                return (float) $optionValue;
            }
        }

        return null;
    }

    protected function appraisalAverageScore(array $scores): string
    {
        $scores = array_values(array_filter($scores, fn ($score) => is_numeric($score)));

        if (empty($scores)) {
            return 'N/A';
        }

        return number_format(array_sum($scores) / count($scores), 2);
    }

    protected function appraisalRatingLabel(array $scores): string
    {
        $scores = array_values(array_filter($scores, fn ($score) => is_numeric($score)));

        if (empty($scores)) {
            return 'N/A';
        }

        $average = array_sum($scores) / count($scores);

        return match (true) {
            $average >= 4.5 => 'Excellent',
            $average >= 3.5 => 'Very Good',
            $average >= 2.5 => 'Good',
            $average >= 1.5 => 'Fair',
            default => 'Poor',
        };
    }

    protected function appraisalAudienceLabel(string $audience, Stakeholder $stakeholder): string
    {
        return match ($audience) {
            self::AUDIENCE_FILL => 'Self',
            'national_president' => 'National President Evaluation',
            default => $this->evaluationAuthorityLabel($stakeholder),
        };
    }

    protected function appraisalMaybeDecodeJson(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        if ($trimmed === '') {
            return $value;
        }

        if (! in_array($trimmed[0] ?? '', ['[', '{'], true)) {
            return $value;
        }

        $decoded = json_decode($trimmed, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    public function queueAppraisalReminderEmails(Stakeholder $target, ?StakeholderAppraisal $appraisal = null): int
    {
        $queued = 0;

        foreach ($this->appraisalReminderPayloads($target, $appraisal) as $payload) {
            EmailService::logEmail($payload);

            $queued++;
        }

        return $queued;
    }

    public function appraisalReminderPayloads(Stakeholder $target, ?StakeholderAppraisal $appraisal = null): array
    {
        $appraisal = $appraisal ?: $this->appraisalRecordForPdf($target);
        $payloads = [];

        if (! $appraisal || $appraisal->self_status !== 'published') {
            $payloads[] = $this->buildReminderPayload($target, 'self');
        }

        if ($appraisal && $appraisal->self_status === 'published' && $appraisal->evaluation_status !== 'published') {
            $evaluator = $appraisal?->evaluator ?: $this->responsibleEvaluatorFor($target);

            if ($evaluator) {
                $payloads[] = $this->buildReminderPayload($target, 'evaluation', $evaluator);
            }
        }

        return array_values(array_filter($payloads));
    }

    public function responsibleEvaluatorFor(Stakeholder $target): ?Stakeholder
    {
        if ($this->isNationalPresident($target)) {
            return Stakeholder::query()
                ->with('designation', 'role')
                ->whereHas('role', function ($query) {
                    $query->where('slug', 'ncp');
                })
                ->orderByDesc('id')
                ->first();
        }

        if ($this->isNecMemberTarget($target)) {
            return Stakeholder::query()
                ->with('designation', 'role')
                ->whereHas('designation', function ($query) {
                    $query->where('name', 'National President');
                })
                ->orderByDesc('id')
                ->first();
        }

        if ($this->isFieldPastorTarget($target)) {
            return Stakeholder::query()
                ->with('designation', 'role')
                ->whereHas('role', function ($query) {
                    $query->where('slug', 'ncp');
                })
                ->orderByDesc('id')
                ->first();
        }

        if ($this->isZonalPastorTarget($target)) {
            return Stakeholder::query()
                ->with('designation', 'role')
                ->whereHas('role', function ($query) {
                    $query->where('slug', 'field-pastor');
                })
                ->where('field_id', $target->field_id)
                ->orderByDesc('id')
                ->first();
        }

        return null;
    }

    protected function buildReminderPayload(Stakeholder $target, string $scope, ?Stakeholder $recipient = null): ?array
    {
        $recipient = $recipient ?: $target;

        if (! $recipient || empty($recipient->email)) {
            return null;
        }

        $stakeholderName = $target->name ?? 'the stakeholder';
        $stakeholderTitle = $target->designation?->name ?? $target->role?->name ?? 'Stakeholder';
        $label = $scope === 'evaluation' ? 'Evaluation Reminder' : 'Appraisal Reminder';
        $subject = $scope === 'evaluation'
            ? "Appraisal Evaluation Reminder for {$stakeholderName}"
            : "Complete Your Appraisal for {$stakeholderName}";

        $content = $scope === 'evaluation'
            ? "
                Dear {$recipient->name},<br><br>
                This is a reminder to complete the evaluation for <strong>{$stakeholderName}</strong> ({$stakeholderTitle}).<br><br>
                Please log in to your dashboard to finish the evaluation when you can.<br><br>
                Thank you.
            "
            : "
                Dear {$recipient->name},<br><br>
                This is a reminder to complete your self appraisal for <strong>{$stakeholderName}</strong> ({$stakeholderTitle}).<br><br>
                Please log in to your dashboard to complete and publish your appraisal when you can.<br><br>
                Thank you.
            ";

        return [
            'recipient' => $recipient->email,
            'subject' => $subject,
            'content' => trim($content),
            'type' => 'appraisal_reminder',
            'reminder_scope' => $scope,
            'appraisee_id' => $target->id,
        ];
    }

    protected function isNecMemberTarget(Stakeholder $user): bool
    {
        $designationName = $user?->designation?->name ?? '';
        $roleSlug = $user?->role?->slug;

        return $roleSlug === 'nec-member'
            || $roleSlug === 'nec'
            || str_contains($designationName, 'National Officer');
    }

    protected function isZonalPastorTarget(Stakeholder $user): bool
    {
        $designationName = $user?->designation?->name ?? '';
        $roleSlug = $user?->role?->slug;

        return $roleSlug === 'zonal-pastor'
            || str_contains($designationName, 'Zonal Pastor')
            || str_contains($designationName, 'Assistant Zonal Pastor');
    }

    protected function isFieldPastorTarget(Stakeholder $user): bool
    {
        return ($user?->role?->slug ?? null) === 'field-pastor';
    }

    protected function syncAnswers(StakeholderAppraisal $appraisal, array $payload, string $audience, int $answeredById): void
    {
        $answers = $payload['answers'] ?? [];
        $questionSlugs = array_keys($answers);

        StakeholderAppraisalAnswer::query()
            ->where('appraisal_id', $appraisal->id)
            ->where('audience', $audience)
            ->where('answered_by_id', $answeredById)
            ->when(! empty($questionSlugs), function ($query) use ($questionSlugs) {
                $query->whereNotIn('question_slug', $questionSlugs);
            }, function ($query) {
                $query->whereRaw('1 = 1');
            })
            ->delete();

        foreach ($answers as $questionSlug => $answerValue) {
            $question = StakeholderReportQuestion::query()
                ->where('slug', $questionSlug)
                ->where('module_type', 'appraisal')
                ->first();

            if (! $question) {
                continue;
            }

            StakeholderAppraisalAnswer::updateOrCreate(
                [
                    'appraisal_id' => $appraisal->id,
                    'audience' => $audience,
                    'question_slug' => $questionSlug,
                    'answered_by_id' => $answeredById,
                ],
                [
                    'question_id' => $question->id,
                    'question_section_id' => $question->section_id,
                    'question_sub_section_id' => $question->sub_section_id,
                    'question_label' => $question->label,
                    'answer_value' => is_array($answerValue) ? json_encode($answerValue) : $answerValue,
                ]
            );
        }
    }

    protected function necMembers(): Collection
    {
        return Stakeholder::query()
            ->with('designation', 'role')
            ->whereIn('role_id', $this->necMemberRoleIds())
            ->get();
    }

    protected function necMemberRoleIds(): array
    {
        return StakeholderRole::whereIn('slug', ['nec-member', 'nec'])->pluck('id')->all();
    }

    protected function zonalPastorRoleIds(): array
    {
        return StakeholderRole::whereIn('slug', ['zonal-pastor'])->pluck('id')->all();
    }

    protected function fieldPastorRoleIds(): array
    {
        return StakeholderRole::whereIn('slug', ['field-pastor'])->pluck('id')->all();
    }

    protected function nationalPresidentDesignationId(): ?int
    {
        return StakeholderDesignation::where('name', 'National President')->value('id');
    }

    protected function isNationalPresident(Stakeholder $user): bool
    {
        return $user?->designation?->name === 'National President';
    }

    public function appraisalPermissionProfile(Stakeholder $user): array
    {
        $designationName = $user?->designation?->name ?? '';
        $roleSlug = $user?->role?->slug;

        if ($designationName === 'National President') {
            return [
                'fill' => ['national-president-fill'],
                'evaluate' => ['nec-member-evaluate'],
            ];
        }

        if ($roleSlug === 'field-pastor') {
            return [
                'fill' => ['field-pastor-fill'],
                'evaluate' => ['field-pastor-evaluate'],
            ];
        }

        if ($roleSlug === 'zonal-pastor' || str_contains($designationName, 'Zonal Pastor')) {
            return [
                'fill' => ['zonal-pastor-fill'],
                'evaluate' => [],
            ];
        }

        if ($roleSlug === 'nec-member' || $roleSlug === 'nec' || str_contains($designationName, 'National Officer')) {
            return [
                'fill' => ['nec-member-fill'],
                'evaluate' => [],
            ];
        }

        if ($roleSlug === 'ncp') {
            return [
                'fill' => [],
                'evaluate' => ['field-pastor-evaluate', 'national-president-evaluate'],
            ];
        }

        return [
            'fill' => [],
            'evaluate' => [],
        ];
    }

    protected function appraisalPermissionAliases(string $permission): array
    {
        $aliases = [
            'field-pastor-fill' => ['appraisal.appraisee'],
            'zonal-pastor-fill' => ['appraisal.appraisee'],
            'nec-member-fill' => ['appraisal.appraisee'],
            'national-president-fill' => ['nec-member-fill', 'appraisal.appraisee'],
            'field-pastor-evaluate' => ['appraisal.appraiser'],
            'ncp-evaluate' => ['appraisal.appraiser'],
            'nec-member-evaluate' => ['national-president-evaluate', 'appraisal.appraiser'],
            'national-president-evaluate' => ['nec-member-evaluate', 'appraisal.appraiser'],
            'appraisal.appraisee' => [
                'field-pastor-fill',
                'zonal-pastor-fill',
                'nec-member-fill',
                'national-president-fill',
            ],
            'appraisal.appraiser' => [
                'field-pastor-evaluate',
                'nec-member-evaluate',
                'national-president-evaluate',
                'ncp-evaluate',
            ],
        ];

        return array_values(array_unique(array_merge([$permission], $aliases[$permission] ?? [])));
    }

    public function hasEvaluationStatus(Stakeholder $user): bool
    {
        $designationName = $user?->designation?->name ?? '';
        $roleSlug = $user?->role?->slug;

        if ($designationName === 'National President') {
            return true;
        }

        if ($roleSlug === 'field-pastor') {
            return true;
        }

        return false;
    }

    protected function hasAnyPermission(Stakeholder $user, array|string|null $permissions): bool
    {
        $permissionList = is_array($permissions) ? $permissions : array_filter([$permissions]);

        if (empty($permissionList)) {
            return false;
        }

        foreach ($permissionList as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
