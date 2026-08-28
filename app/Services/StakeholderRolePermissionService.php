<?php

namespace App\Services;

use App\Models\Stakeholder;
use App\Models\StakeholderRole;
use Illuminate\Support\Collection;
use App\Models\StakeholderPermission;
use Illuminate\Support\Facades\Cache;
use App\Models\StakeholderReportQuestion;

class StakeholderRolePermissionService
{
    public function sectionAccess($user, $model): array
    {
        if (empty($model->access_roles)) {
            return ['view' => true];
        }

        $userAccessIds = [];

        if (($model->module_type ?? 'report') === 'appraisal') {
            if ($user) {
                $userAccessIds = collect($user->permissions())->pluck('id')->all();

                if (method_exists($user, 'roles')) {
                    $userAccessIds = array_merge(
                        $userAccessIds,
                        $user->relationLoaded('roles')
                            ? $user->roles->pluck('id')->toArray()
                            : $user->roles()->pluck('id')->toArray()
                    );
                }

                if (isset($user->role_id) && $user->role_id) {
                    $userAccessIds[] = $user->role_id;
                }

                if (isset($user->designation_id) && $user->designation_id) {
                    $userAccessIds[] = $user->designation_id;
                }
            }

            return [
                'view' => count(array_intersect($userAccessIds, $model->access_roles)) > 0,
            ];
        }

        if ($user) {
            if (method_exists($user, 'roles')) {
                $userAccessIds = array_merge(
                    $userAccessIds,
                    $user->relationLoaded('roles')
                        ? $user->roles->pluck('id')->toArray()
                        : $user->roles()->pluck('id')->toArray()
                );
            }

            if (isset($user->role_id) && $user->role_id) {
                $userAccessIds[] = $user->role_id;
            }
        }

        return [
            'view' => count(array_intersect($userAccessIds, $model->access_roles)) > 0
        ];
    }

    public function questionAccess($user, $question, $isAdmin=false): array
    {
        if($isAdmin){
            return ['view' => true, 'edit' => true];
        }
        
        if ($question->permissions->isEmpty()) {
            return ['view' => true, 'edit' => true];
        }

        if (!$user || !$user->role) {
            return ['view' => false, 'edit' => false];
        }

        if (!$user->role->relationLoaded('permissions')) {
            $user->role->load('permissions');
        }

        if (!$user->relationLoaded('designation')) {
            $user->load('designation:id,name');
        }

        $rolePermissionIds = $user->role->permissions
            ->pluck('id')
            ->toArray();

        $questionPermissionIds = $question->permissions
            ->pluck('id')
            ->toArray();

        $hasPermission = !empty(array_intersect($rolePermissionIds, $questionPermissionIds));

        if (($question->module_type ?? 'report') !== 'appraisal') {
            return [
                'view' => true,
                'edit' => $hasPermission,
            ];
        }

        if (
            $user->access_appraisal_evaluation
            && ($user?->designation?->name === 'National President')
            && in_array($question->audience ?? 'fill', ['evaluate', 'national_president'], true)
        ) {
            return ['view' => true, 'edit' => true];
        }

        $requiredSlug = $this->appraisalQuestionPermissionSlug($question->permissions->pluck('slug')->all());

        if (! $requiredSlug) {
            return ['view' => true, 'edit' => true];
        }

        if (app(AppraisalService::class)->hasAppraisalPermission($user, $requiredSlug)) {
            return ['view' => true, 'edit' => true];
        }

        return [
            'view' => false,
            'edit' => false,
        ];
    }


    /**
     * Check if stakeholder has a permission (cached).
     */
    public function stakeholderHasPermission(Stakeholder $stakeholder, string $permissionSlug): bool
    {
        return $stakeholder->roles()
            ->whereHas('permissions',fn($q) =>
                $q->where('slug', $permissionSlug)
            )->exists();
    }

    /**
     * Get all permissions for stakeholder (cached).
     */
    public function getStakeholderPermissions(Stakeholder $stakeholder): Collection
    {
        return Cache::remember(
            $this->stakeholderPermissionCacheKey($stakeholder),
            now()->addMinutes(30),
            function () use ($stakeholder) {
                return $stakeholder->roles()
                    ->with('permissions:id,slug')
                    ->get()
                    ->pluck('permissions')
                    ->flatten()
                    ->unique('id')
                    ->values();
            }
        );
    }

    protected function appraisalQuestionPermissionSlug(array $questionPermissionSlugs): ?string
    {
        $knownSlugs = [
            'field-pastor-fill',
            'zonal-pastor-fill',
            'nec-member-fill',
            'nec-member-evaluate',
            'field-pastor-evaluate',
            'national-president-fill',
            'national-president-evaluate',
            'ncp-evaluate',
            'appraisal.appraisee',
            'appraisal.appraiser',
        ];

        foreach ($questionPermissionSlugs as $slug) {
            if (in_array($slug, $knownSlugs, true)) {
                return $slug;
            }
        }

        return null;
    }

}
