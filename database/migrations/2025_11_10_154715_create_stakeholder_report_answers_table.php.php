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
        if (Schema::hasTable('stakeholder_report_answers')) {
            return;
        }

        Schema::create('stakeholder_report_answers', function (Blueprint $table) {
            $table->id();
            $table->integer('report_id');
            $table->integer('question_id');
            $table->text('answer_value')->nullable();
            $table->timestamps();

            $table->unique(['report_id', 'question_id']); // avoid duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
