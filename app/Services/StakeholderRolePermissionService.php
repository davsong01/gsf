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

        $userRoles = $user->roles->pluck('id')->toArray();

        return [
            'view' => count(array_intersect($userRoles, $model->access_roles)) > 0
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

        $rolePermissionIds = $user->role->permissions
            ->pluck('id')
            ->toArray();

        $questionPermissionIds = $question->permissions
            ->pluck('id')
            ->toArray();

        $hasPermission = !empty(array_intersect(
            $rolePermissionIds,
            $questionPermissionIds
        ));

        return [
            'view' => true,
            'edit' => $hasPermission,
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

}
