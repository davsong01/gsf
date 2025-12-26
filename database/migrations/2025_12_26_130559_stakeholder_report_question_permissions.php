<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stakeholder_report_question_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stakeholder_report_question_id')->nullable();
            $table->foreignId('stakeholder_permission_id')->nullable();
            $table->timestamps();

            $table->unique(['stakeholder_report_question_id', 'stakeholder_permission_id'], 'srq_permission_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stakeholder_report_question_permissions');
    }
};
