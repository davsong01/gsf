<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'stakeholder_question_sub_sections',
            'stakeholder_report_questions',
            'stakeholder_question_sections',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->json('access_roles')->nullable()->after('id'); // remove default
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'stakeholder_question_sub_sections',
            'stakeholder_report_questions',
            'stakeholder_question_sections',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('access_roles');
            });
        }
    }
};
