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

    /**
     * Check if stakeholder has ANY of the given permissions.
     */
    public function stakeholderHasAnyPermission(Stakeholder $stakeholder, array $permissionSlugs): bool
    {
        return $this->getStakeholderPermissions($stakeholder)
            ->pluck('slug')
            ->intersect($permissionSlugs)
            ->isNotEmpty();
    }

    /**
     * Check if stakeholder has ALL given permissions.
     */
    public function stakeholderHasAllPermissions(Stakeholder $stakeholder, array $permissionSlugs): bool
    {
        return collect($permissionSlugs)->every(
            fn ($slug) => $this->stakeholderHasPermission($stakeholder, $slug)
        );
    }

    /**
     * Clear stakeholder permission cache.
     */
    protected function forgetStakeholderPermissionCache(Stakeholder $stakeholder): void
    {
        Cache::forget($this->stakeholderPermissionCacheKey($stakeholder));
    }

    /**
     * Clear role permission cache (future use).
     */
    protected function forgetRolePermissionCache(StakeholderRole $role): void
    {
        Cache::forget("stakeholder_role_permissions_{$role->id}");
    }

    /**
     * Cache key generator.
     */
    protected function stakeholderPermissionCacheKey(Stakeholder $stakeholder): string
    {
        return "stakeholder_permissions_{$stakeholder->id}";
    }

    public function getSectionAccess(Stakeholder $stakeholder, $sectionOrSubsection): array
    {
        // Roles allowed to access this section/subsection
        $allowedRoles = $sectionOrSubsection->access_roles ?? [];

        // Stakeholder roles
        $roles = $stakeholder->roles->pluck('id')->toArray();

        $canView = !empty(array_intersect($roles, $allowedRoles));

        return [
            'view' => $canView,
        ];
    }
}
