<?php

namespace App\Services;

use App\Models\Stakeholder;
use App\Models\StakeholderRole;
use App\Models\StakeholderPermission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StakeholderRolePermissionService
{
    /**
     * Assign roles to a stakeholder (overwrite existing).
     */
    public function syncRolesToStakeholder(Stakeholder $stakeholder, array $roleIds): void
    {
        $stakeholder->roles()->sync($roleIds);
        $this->forgetStakeholderPermissionCache($stakeholder);
    }

    /**
     * Add roles to a stakeholder (without detaching existing).
     */
    public function addRolesToStakeholder(Stakeholder $stakeholder, array $roleIds): void
    {
        $stakeholder->roles()->syncWithoutDetaching($roleIds);
        $this->forgetStakeholderPermissionCache($stakeholder);
    }

    /**
     * Remove roles from a stakeholder.
     */
    public function removeRolesFromStakeholder(Stakeholder $stakeholder, array $roleIds): void
    {
        $stakeholder->roles()->detach($roleIds);
        $this->forgetStakeholderPermissionCache($stakeholder);
    }

    /**
     * Assign permissions to a role (overwrite existing).
     */
    public function syncPermissionsToRole(StakeholderRole $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
        $this->forgetRolePermissionCache($role);
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
}
