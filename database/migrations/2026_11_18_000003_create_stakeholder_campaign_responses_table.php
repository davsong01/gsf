<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stakeholder_campaign_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('stakeholder_campaigns')->cascadeOnDelete();
            $table->json('answers');
            $table->dateTime('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stakeholder_campaign_responses');
    }
};
