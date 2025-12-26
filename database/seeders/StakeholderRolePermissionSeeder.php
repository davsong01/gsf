<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StakeholderRole;
use App\Models\StakeholderPermission;
use App\Services\StakeholderRolePermissionService;

// php artisan db:seed --class=StakeholderRolePermissionSeeder
class StakeholderRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $service = new StakeholderRolePermissionService();

        $rolesPermissions = [
            'Secretariat' => [
                'report.questions.view_chapter_section',
                'report.questions.view_zone_section',
                'report.questions.view_secretariat_section',
                'report.questions.view_ncp_section',
                'report.questions.modify.secretariat_section',
            ],
            'NCP' => [
                'report.ncp.approve',
                'report.ncp.decline',
                'report.questions.view_zone_section',
                'report.questions.view_field_section',
                'report.questions.view_secretariat_section',
                'report.questions.modify.ncp_section',
            ],
            'Field Pastor' => [
                'report.field.approve',
                'report.field.decline',
                'report.questions.view_chapter_section',
                'report.questions.view_zone_section',
                'report.questions.view_secretariat_section',
                'report.questions.view_ncp_section',
                'report.questions.modify.field_section',
            ],
            'Zonal Pastor' => [
                'report.zone.approve',
                'report.zone.decline',
                'report.questions.view_chapter_section',
                'report.questions.view_zone_section',
                'report.questions.view_secretariat_section',
                'report.questions.view_ncp_section',
                'report.questions.modify.zone_section',
            ],
            'Chapter Representative' => [
                'report.questions.view_chapter_section',
                'report.questions.view_zone_section',
                'report.questions.view_secretariat_section',
                'report.questions.view_ncp_section',
                'report.questions.modify.chapter_section',
            ],
        ];

        foreach ($rolesPermissions as $roleName => $permissionSlugs) {

            // Create or get the role
            $role = StakeholderRole::firstOrCreate(
                ['name' => $roleName],
                ['slug' => \Str::slug($roleName)]
            );

            $permissionIds = [];

            foreach ($permissionSlugs as $slug) {
                // Create permission if it doesn't exist
                $permission = StakeholderPermission::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => ucwords(str_replace(['.', '_'], ' ', $slug))]
                );
                $permissionIds[] = $permission->id;
            }

            // Sync permissions to role
            $service->syncPermissionsToRole($role, $permissionIds);
        }
    }
}
