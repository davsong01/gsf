<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stakeholder_report_answers', function (Blueprint $table) {
            $table->integer('question_section_id')->nullable()->after('question_id');
            $table->integer('question_sub_section_id')->nullable()->after('question_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stakeholder_report_answers', function (Blueprint $table) {
            $table->dropColumn('question_section_id');
            $table->dropColumn('question_sub_section_id');
        });
    }
};
