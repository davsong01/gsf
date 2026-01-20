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
            $table->integer('answer_quantity')->after('answer_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stakeholder_report_answers', function (Blueprint $table) {
            $table->dropColumn('answer_quantity');
        });
    }
};
