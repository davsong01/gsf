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
        // Create stakeholder_designations table
        Schema::create('stakeholder_designations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('field_id')->nullable();
            $table->integer('zone_id')->nullable();
            $table->integer('chapter_id')->nullable();
            $table->integer('order')->default(0)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('type', ['nec', 'chapter_executive'])->default('nec');
            $table->timestamps();
        });

        // Modify stakeholders table
        Schema::table('stakeholders', function (Blueprint $table) {
            // Add tenure and gender
            $table->string('tenure')->nullable()->after('role_id');
            $table->enum('gender', ['Male', 'Female'])->nullable()->after('tenure');
            $table->integer('designation_id')->nullable()->after('tenure');
            // Rename portfolio to office
            $table->renameColumn('portfolio', 'office');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop stakeholder_designations table
        Schema::dropIfExists('stakeholder_designations');

        // Revert stakeholders table changes
        Schema::table('stakeholders', function (Blueprint $table) {
            // Drop added columns
            $table->dropColumn(['tenure', 'gender','designation_id']);

            // Rename office back to portfolio
            $table->renameColumn('office', 'portfolio');
        });
    }
};
