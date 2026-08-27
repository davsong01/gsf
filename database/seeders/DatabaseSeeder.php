<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AppraisalQuestionSeeder;
use Database\Seeders\AppraisalRolePermissionSeeder;
use Database\Seeders\StakeholderQuestionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // StakeholderQuestionSeeder::class,
            AppraisalQuestionSeeder::class,
            AppraisalRolePermissionSeeder::class,
        ]);
    }
}
