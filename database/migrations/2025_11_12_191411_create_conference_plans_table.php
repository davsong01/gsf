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
        Schema::create('conference_plans', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0);
            $table->integer('conference_edition_id');
            $table->string('title')->nullable();
            $table->json('items')->nullable();
            $table->decimal('price', 11,2)->nullable();
            $table->string('type')->default('single'); // multiple
            $table->string('level')->default('Participant'); // Participant, Moderator, Alumni, etc
            $table->json('registration_fields')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_plans');
    }
};
