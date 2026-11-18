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
            ['label' => 'Email Address', 'type' => 'text', 'is_required' => true],
            ['label' => 'Phone Number', 'type' => 'text', 'is_required' => true],
            ['label' => 'Fellowship or Organization', 'type' => 'text', 'is_required' => true],
            ['label' => 'State or Region', 'type' => 'text', 'is_required' => true],
            [
                'label' => 'Area of Interest',
                'type' => 'select',
                'is_required' => true,
                'options' => ['Leadership', 'Community Outreach', 'Training', 'Worship and Media', 'Other'],
            ],
            [
                'label' => 'How did you hear about this campaign?',
                'type' => 'select',
                'is_required' => true,
                'options' => ['Social Media', 'Church or Fellowship', 'Friend', 'Website', 'Other'],
            ],
            ['label' => 'What do you hope to gain from this campaign?', 'type' => 'textarea', 'is_required' => true],
            ['label' => 'Relevant Experience', 'type' => 'textarea', 'is_required' => false],
            ['label' => 'Additional Comments', 'type' => 'textarea', 'is_required' => false],
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
