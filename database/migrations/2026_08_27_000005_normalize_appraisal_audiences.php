<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('stakeholder_appraisal_answers')
            ->where('audience', 'appraisee')
            ->update(['audience' => 'fill']);

        DB::table('stakeholder_appraisal_answers')
            ->whereIn('audience', ['appraiser', 'national_president'])
            ->update(['audience' => 'evaluate']);

        DB::table('stakeholder_report_questions')
            ->where('module_type', 'appraisal')
            ->where('audience', 'appraisee')
            ->update(['audience' => 'fill']);

        DB::table('stakeholder_report_questions')
            ->where('module_type', 'appraisal')
            ->whereIn('audience', ['appraiser', 'national_president'])
            ->update(['audience' => 'evaluate']);
    }

    public function down(): void
    {
        DB::table('stakeholder_appraisal_answers')
            ->where('audience', 'fill')
            ->update(['audience' => 'appraisee']);

        DB::table('stakeholder_appraisal_answers')
            ->where('audience', 'evaluate')
            ->update(['audience' => 'appraiser']);

        DB::table('stakeholder_report_questions')
            ->where('module_type', 'appraisal')
            ->where('audience', 'fill')
            ->update(['audience' => 'appraisee']);

        DB::table('stakeholder_report_questions')
            ->where('module_type', 'appraisal')
            ->where('audience', 'evaluate')
            ->update(['audience' => 'appraiser']);
    }
};
