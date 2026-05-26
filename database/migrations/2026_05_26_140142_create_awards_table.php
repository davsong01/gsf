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
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('reference')->nullable();

            $table->integer('chapter_id')->nullable();
            $table->integer('zone_id')->nullable();
            $table->integer('zone_status')->nullable();
            $table->string('zone_comment')->nullable();

            $table->integer('field_id')->nullable();
            $table->integer('field_status')->nullable();
            $table->string('field_comment')->nullable();

            $table->integer('national_status')->nullable();
            $table->string('national_comment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
