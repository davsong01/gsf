<?php

namespace Database\Seeders;

use App\Models\StakeholderPermission;
use App\Models\StakeholderRole;
use Illuminate\Database\Seeder;

// php artisan db:seed --class=AppraisalRolePermissionSeeder
class AppraisalRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionMap = [
            'Field Pastor' => [
                'field-pastor-fill',
                'field-pastor-evaluate',
            ],
            'Zonal Pastor' => [
                'zonal-pastor-fill',
            ],
            'Nec Member' => [
                'nec-member-fill',
            ],
            'National President' => [
                'national-president-fill',
                'nec-member-evaluate',
            ],
            'NCP' => [
                'field-pastor-evaluate',
                'national-president-evaluate',
                'ncp-evaluate',
            ],
        ];

        foreach ($permissionMap as $roleName => $permissionSlugs) {
            $role = StakeholderRole::firstOrCreate(
                ['name' => $roleName],
                ['slug' => \Str::slug($roleName)]
            );

            $permissionIds = StakeholderPermission::whereIn('slug', $permissionSlugs)
                ->pluck('id')
                ->all();

            if (!empty($permissionIds)) {
                $role->permissions()->syncWithoutDetaching($permissionIds);
            }
        }
    }
}
