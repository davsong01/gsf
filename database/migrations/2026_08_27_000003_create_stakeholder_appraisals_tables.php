<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('stakeholder_appraisals')) {
            Schema::create('stakeholder_appraisals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appraisee_id')->constrained('stakeholders')->cascadeOnDelete();
                $table->foreignId('evaluator_id')->nullable()->constrained('stakeholders')->nullOnDelete();
                $table->string('self_status')->default('draft');
                $table->string('evaluation_status')->default('draft');
                $table->timestamp('self_published_at')->nullable();
                $table->timestamp('evaluation_published_at')->nullable();
                $table->timestamps();
                $table->unique('appraisee_id');
            });
        }

        if (! Schema::hasTable('stakeholder_appraisal_answers')) {
            Schema::create('stakeholder_appraisal_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appraisal_id')->constrained('stakeholder_appraisals')->cascadeOnDelete();
                $table->foreignId('question_id')->nullable()->constrained('stakeholder_report_questions')->nullOnDelete();
                $table->foreignId('question_section_id')->nullable()->constrained('stakeholder_question_sections')->nullOnDelete();
                $table->foreignId('question_sub_section_id')->nullable()->constrained('stakeholder_question_sub_sections')->nullOnDelete();
                $table->foreignId('answered_by_id')->nullable()->constrained('stakeholders')->nullOnDelete();
                $table->string('audience')->default('fill');
                $table->string('question_slug')->nullable();
                $table->text('question_label')->nullable();
                $table->longText('answer_value')->nullable();
                $table->timestamps();
                $table->unique(['appraisal_id', 'audience', 'question_slug'], 'appraisal_answer_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stakeholder_appraisal_answers');
        Schema::dropIfExists('stakeholder_appraisals');
    }
};
