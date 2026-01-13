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
        Schema::dropIfExists('stakeholder_reports');
        
        if (!Schema::hasTable('stakeholder_reports')) {
            Schema::create('stakeholder_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('stakeholder_id');
            $table->integer('chapter_id')->nullable();
            $table->integer('zone_id')->nullable();
            $table->integer('field_id')->nullable();

            // approval statuses
            $table->tinyInteger('field_status')->default(0);
            $table->tinyInteger('zone_status')->default(0);
            $table->tinyInteger('national_status')->default(0);

            $table->timestamp('field_approved_at')->nullable();
            $table->timestamp('field_rejected_at')->nullable();
            $table->timestamp('zone_approved_at')->nullable();
            $table->timestamp('zone_rejected_at')->nullable();
            $table->timestamp('national_approved_at')->nullable();
            $table->timestamp('national_rejected_at')->nullable();

            $table->string('field_comment')->nullable();
            $table->string('zone_comment')->nullable();
            $table->string('national_comment')->nullable();

            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
