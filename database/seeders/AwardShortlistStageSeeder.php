<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\AwardShortlistStage;

class AwardShortlistStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'title' => 'Stage 1',
                'slug' => 'first',
                'position' => 1,
                'active' => true,
                'mark_as_final' => true
            ],
            [
                'title' => 'Stage 2',
                'slug' => 'second',
                'position' => 2,
                'active' => true,
                'mark_as_final' => false
            ],
            [
                'title' => 'Stage 3',
                'slug' => 'third',
                'position' => 3,
                'active' => true,
                'mark_as_final' => false
            ],
        ];

        foreach ($stages as $stage) {
            AwardShortlistStage::updateOrCreate(
                ['slug' => $stage['slug']],
                [
                    'title' => $stage['title'],
                    'position' => $stage['position'],
                    'active' => $stage['active'],
                ]
            );
        }
    }
}
