<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('conference_editions', 'registration_tickers_enabled')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->boolean('registration_tickers_enabled')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('conference_editions', 'registration_tickers_enabled')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->dropColumn('registration_tickers_enabled');
            });
        }
    }
};
