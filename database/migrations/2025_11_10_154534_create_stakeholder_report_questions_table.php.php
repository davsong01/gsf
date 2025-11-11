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
        Schema::dropIfExists('stakeholder_report_questions');

        Schema::create('stakeholder_report_questions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // e.g. total_members, funds_collected
            $table->string('label'); // e.g. "Total Members"
            $table->string('type')->default('text'); // text, number, select, textarea, etc.
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // for dropdowns or radios
            $table->integer('order')->default(0);
            $table->string('width_class')->default('col-md-6');
            $table->string('section_id')->nullable(); // optional (e.g. 'Personal Section')
            $table->string('sub_section_id')->nullable();
            $table->string('role')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->boolean('is_quantifiable')->default(false);
            
            $table->timestamps();
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
