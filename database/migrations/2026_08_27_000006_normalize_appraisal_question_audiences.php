<?php

use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderQuestionSubSection;
use App\Models\StakeholderReportQuestion;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $questions = StakeholderReportQuestion::query()
            ->where('module_type', 'appraisal')
            ->with(['section', 'subsection'])
            ->get();

        foreach ($questions as $question) {
            $sectionName = strtolower((string) ($question->section?->name ?? ''));
            $subSectionName = strtolower((string) ($question->subsection?->name ?? ''));

            $isEvaluatorSection = str_contains($sectionName, 'official use only')
                || str_contains($sectionName, 'general observations and recommendations')
                || str_contains($subSectionName, 'evaluator assessment');

            $question->update([
                'audience' => $isEvaluatorSection ? 'evaluate' : 'fill',
            ]);
        }
    }

    public function down(): void
    {
        $questions = StakeholderReportQuestion::query()
            ->where('module_type', 'appraisal')
            ->with(['section', 'subsection'])
            ->get();

        foreach ($questions as $question) {
            $sectionName = strtolower((string) ($question->section?->name ?? ''));
            $subSectionName = strtolower((string) ($question->subsection?->name ?? ''));

            $isEvaluatorSection = str_contains($sectionName, 'official use only')
                || str_contains($sectionName, 'general observations and recommendations')
                || str_contains($subSectionName, 'evaluator assessment');

            $question->update([
                'audience' => $isEvaluatorSection ? 'appraiser' : 'appraisee',
            ]);
        }
    }
};
