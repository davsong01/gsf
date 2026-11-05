<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapter_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->string('day_of_week', 9);
            $table->time('time');
            $table->string('venue');
            $table->timestamps();
            $table->index(['chapter_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_meetings');
    }
};
