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
        if (!Schema::hasColumn('conference_editions', 'ministry')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                if (!Schema::hasColumn('conference_editions', 'ministry')) {
                    $table->string('ministry')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('conference_editions', 'ministry')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->dropColumn('ministry');
            });
        }
    }
};
