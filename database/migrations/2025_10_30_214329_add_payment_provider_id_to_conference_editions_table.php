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
        Schema::table('conference_editions', function (Blueprint $table) {
            if (!Schema::hasColumn('conference_editions', 'payment_provider_id')) {
                $table->integer('payment_provider_id')->nullable()->after('id');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_provider_id')) {
                $table->integer('payment_provider_id')->nullable()->after('id');
            }
        });      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_editions', function (Blueprint $table) {
            if (Schema::hasColumn('conference_editions', 'payment_provider_id')) {
                $table->dropColumn('payment_provider_id');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_provider_id')) {
                $table->dropColumn('payment_provider_id');
            }
        });
    }
};
