<?php

namespace App\Services;

use App\Models\Stakeholder;
use App\Models\StakeholderAppraisal;
use App\Models\StakeholderAppraisalAnswer;
use App\Models\StakeholderDesignation;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderRole;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppraisalService
{
    public const AUDIENCE_FILL = 'fill';
    public const AUDIENCE_EVALUATE = 'evaluate';

    public function dashboardAccess(Stakeholder $user): array
    {
        $permissionProfile = $this->appraisalPermissionProfile($user);

        $canSelfAppraise = $this->hasAnyPermission($user, $permissionProfile['fill'] ?? []);

        $canEvaluate = $this->hasAnyPermission($user, $permissionProfile['evaluate'] ?? []);
        $isNationalPresident = $this->isNationalPresident($user);

        return [
            'my_appraisal' => $canSelfAppraise,
            'evaluations' => $canEvaluate,
            self::AUDIENCE_FILL => $canSelfAppraise,
            self::AUDIENCE_EVALUATE => $canEvaluate,
            'national_president' => $isNationalPresident,
        ];
    }

    public function canAccessMode(Stakeholder $user, string $mode): bool
    {
        $access = $this->dashboardAccess($user);

        return (bool) ($access[$mode] ?? false);
    }

    public function structure(Stakeholder $user, array $audiences): Collection
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

        return $sections->map(function ($section) use ($permissionService, $user, $audiences) {
            if (! $permissionService->sectionAccess($user, $section)['view']) {
                return null;
            }

            $subsections = $section->subsections
                ->map(function ($subsection) use ($permissionService, $user, $audiences) {
                    if (! $permissionService->sectionAccess($user, $subsection)['view']) {
                        return null;
                    }

                    $questions = $subsection->questions
                        ->filter(function ($question) use ($permissionService, $user, $audiences) {
                            if (! in_array($question->audience ?? self::AUDIENCE_FILL, $audiences, true)) {
                                return false;
                            }

                            return $permissionService->questionAccess($user, $question)['view'];
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

    public function structureForMode(Stakeholder $user, string $mode): Collection
    {
        return match ($mode) {
            'my', self::AUDIENCE_FILL => $this->structure($user, [self::AUDIENCE_FILL]),
            'evaluations', self::AUDIENCE_EVALUATE => $this->structure($user, [self::AUDIENCE_EVALUATE]),
            default => collect(),
        };
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
        $period = 'May 2025 – August 2026';
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
            return self::AUDIENCE_EVALUATE;
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

    public function prepareSubmissionAnswers(
        Stakeholder $user,
        Request $request,
        string $mode,
        string $status,
        ?StakeholderAppraisal $appraisal = null,
        ?Stakeholder $target = null
    ): array {
        $sections = $this->structureForMode($user, $mode);
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
