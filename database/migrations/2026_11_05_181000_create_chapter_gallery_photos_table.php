<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapter_gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption', 255)->nullable();
            $table->timestamps();

            $table->index(['chapter_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_gallery_photos');
    }
};
