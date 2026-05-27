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
        Schema::create('award_settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('allow_chapter_edit')->default(0);
            $table->tinyInteger('allow_chapter_comment')->default(0);
            $table->tinyInteger('allow_chapter_approval')->default(0);

            $table->tinyInteger('allow_zone_edit')->default(0);
            $table->tinyInteger('allow_zone_comment')->default(0);
            $table->tinyInteger('allow_zone_approval')->default(0);

            $table->tinyInteger('allow_field_edit')->default(0);
            $table->tinyInteger('allow_field_comment')->default(0);
            $table->tinyInteger('allow_field_approval')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('award_settings');
    }
};
