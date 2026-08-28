<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'stakeholder_question_sections',
            'stakeholder_question_sub_sections',
            'stakeholder_report_questions',
        ] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'module_type')) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                    if ($table === 'stakeholder_report_questions') {
                        $tableBlueprint->string('module_type')->default('report')->after('status');
                        return;
                    }

                    $tableBlueprint->string('module_type')->default('report')->after('name');
                });

                DB::table($table)->whereNull('module_type')->update(['module_type' => 'report']);
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'stakeholder_question_sections',
            'stakeholder_question_sub_sections',
            'stakeholder_report_questions',
        ] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'module_type')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->dropColumn('module_type');
                });
            }
        }
    }
};
