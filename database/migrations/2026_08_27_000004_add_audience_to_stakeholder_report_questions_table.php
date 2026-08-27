<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stakeholder_report_questions', function (Blueprint $table) {
            if (! Schema::hasColumn('stakeholder_report_questions', 'audience')) {
                $table->string('audience')->default('fill')->after('type');
            }
        });

        DB::table('stakeholder_report_questions')
            ->whereNull('audience')
            ->update(['audience' => 'fill']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stakeholder_report_questions', function (Blueprint $table) {
            if (Schema::hasColumn('stakeholder_report_questions', 'audience')) {
                $table->dropColumn('audience');
            }
        });
    }
};
