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
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('inactive')->index();
            $table->string('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('base_url')->nullable();
            $table->float('provider_charge')->nullable();
            $table->boolean('customer_pays_provider_charge')->default(false);
            $table->boolean('allow_sub_account')->default(false);
            $table->string('api_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('public_key')->nullable();
            $table->json('channels')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};
