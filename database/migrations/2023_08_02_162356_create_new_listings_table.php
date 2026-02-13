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
            if (!Schema::hasTable('new_listings')) {
                Schema::create('new_listings', function (Blueprint $table) {
                    $table->id();
                    $table->string('name')->nullable();
                    $table->string('phone')->nullable();
                    $table->string('campus')->nullable();
                    $table->string('portfolio')->nullable();
                    $table->string('email')->nullable();
                    $table->string('gender')->nullable();
                    $table->string('matriculation_year')->nullable();
                    $table->string('graduation_year')->nullable();
                    $table->string('office')->nullable();
                    $table->timestamps();
                });
            }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_listings');
    }
};
