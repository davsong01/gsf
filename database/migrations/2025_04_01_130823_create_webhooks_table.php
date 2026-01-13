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
        if (!Schema::hasTable('webhooks')) {
            Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('event_type')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('reference')->nullable();
            $table->json('payload')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
