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
        if (!Schema::hasTable('conference_schedules')) {
            Schema::create('conference_schedules', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0);
            $table->integer('conference_edition_id');
            $table->string('day')->nullable();
            $table->string('date')->nullable();
            $table->json('sessions')->nullable();
            
            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_schedules');
    }
};
