<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stakeholder_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('instructions');
            $table->dateTime('open_from');
            $table->dateTime('open_until');
            $table->timestamps();
        });

        Schema::create('stakeholder_campaign_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('stakeholder_campaigns')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('stakeholder_report_questions')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['campaign_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stakeholder_campaign_questions');
        Schema::dropIfExists('stakeholder_campaigns');
    }
};
