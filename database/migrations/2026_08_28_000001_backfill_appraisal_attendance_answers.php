<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $forms = [
            'field-pastor',
            'zonal-pastor',
            'national-officer',
            'national-president',
        ];

        $suffixes = [
            'nec-orientation-programme',
            'nec-leadership-retreat',
            'general-assembly-2026',
            'general-assembly-2026-january-south-west',
            'general-assembly-2026-february-northern',
            'general-assembly-2026-march-south-south-eastern',
            'national-prayer-conference-2026',
        ];

        foreach ($forms as $form) {
            foreach ($suffixes as $suffix) {
                $slug = "{$form}-{$suffix}";

                DB::table('stakeholder_appraisal_answers')
                    ->where('question_slug', $slug)
                    ->where('answer_value', 'attended')
                    ->update(['answer_value' => 1]);

                DB::table('stakeholder_appraisal_answers')
                    ->where('question_slug', $slug)
                    ->where('answer_value', 'absent')
                    ->update(['answer_value' => 0]);
            }
        }
    }

    public function down(): void
    {
        $forms = [
            'field-pastor',
            'zonal-pastor',
            'national-officer',
            'national-president',
        ];

        $suffixes = [
            'nec-orientation-programme',
            'nec-leadership-retreat',
            'general-assembly-2026',
            'general-assembly-2026-january-south-west',
            'general-assembly-2026-february-northern',
            'general-assembly-2026-march-south-south-eastern',
            'national-prayer-conference-2026',
        ];

        foreach ($forms as $form) {
            foreach ($suffixes as $suffix) {
                $slug = "{$form}-{$suffix}";

                DB::table('stakeholder_appraisal_answers')
                    ->where('question_slug', $slug)
                    ->where('answer_value', 1)
                    ->update(['answer_value' => 'attended']);

                DB::table('stakeholder_appraisal_answers')
                    ->where('question_slug', $slug)
                    ->where('answer_value', 0)
                    ->update(['answer_value' => 'absent']);
            }
        }
    }
};
