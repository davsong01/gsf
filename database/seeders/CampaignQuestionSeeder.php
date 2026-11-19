<?php

namespace Database\Seeders;

use App\Models\StakeholderReportQuestion;
use Illuminate\Database\Seeder;

class CampaignQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['label' => 'Full Name', 'type' => 'text', 'is_required' => true],
            ['label' => 'Email', 'type' => 'email', 'is_required' => true],
            ['label' => 'Phone', 'type' => 'text', 'is_required' => true],
            [
                'label' => 'Gender',
                'type' => 'select',
                'is_required' => true,
                'options' => ['Male', 'Female'],
            ],
            [
                'label' => 'Day of Arrival',
                'type' => 'select',
                'is_required' => true,
                'options' => ['Friday', 'Saturday'],
            ],
            [
                'label' => 'Coming with Family?',
                'type' => 'select',
                'is_required' => true,
                'options' => ['Yes', 'No'],
            ],
        ];

        foreach ($questions as $order => $question) {
            $label = $question['label'];
            StakeholderReportQuestion::updateOrCreate(
                ['slug' => 'campaign-' . \Illuminate\Support\Str::slug($label)],
                [
                    'label' => $label,
                    'type' => $question['type'],
                    'is_required' => $question['is_required'],
                    'options' => $question['options'] ?? null,
                    'order' => $order + 1,
                    'width_class' => $question['type'] === 'textarea' ? 'col-md-12' : 'col-md-6',
                    'section_id' => null,
                    'sub_section_id' => null,
                    'status' => 1,
                    'is_quantifiable' => false,
                    'module_type' => 'campagn',
                ]
            );
        }
    }
}
