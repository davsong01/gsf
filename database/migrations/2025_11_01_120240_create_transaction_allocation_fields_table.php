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
        Schema::create('transaction_allocation_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable();
            $table->string('key'); // e.g., chapter_id, assembly_id
            $table->string('value'); // actual selected value
            $table->timestamps();

            $table->index(['key', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_allocation_fields');
    }
};
