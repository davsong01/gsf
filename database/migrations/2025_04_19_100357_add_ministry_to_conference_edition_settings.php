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
        if (Schema::hasColumn('conference_editions', 'ministry')) {
            return;
        }

        Schema::table('conference_editions', function (Blueprint $table) {
            $table->string('ministry')->nullable();
        });
        
        Schema::table('conference_editions', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_edition_settings', function (Blueprint $table) {
            //
        });
    }
};
