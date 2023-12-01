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
        Schema::create('temp_members', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("sex")->nullable();
            $table->string("status")->nullable();
            $table->string("passport")->nullable();
            $table->string("chapter")->nullable();
            $table->string("role")->nullable();
            $table->string("program")->nullable();
            $table->string("course")->nullable();
            $table->string("open_to_work")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_members');
    }
};
