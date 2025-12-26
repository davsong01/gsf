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
        Schema::create('stakeholder_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Edit Report
            $table->string('slug')->unique(); // report.edit
            $table->string('type')->nullable(); // question,action
            $table->string('description')->nullable(); // report, approval
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stakeholder_permissions');
    }
};
