<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add the new column if it doesn't exist
        if (!Schema::hasColumn('critical_emails', 'conference_edition_id')) {
            Schema::table('critical_emails', function (Blueprint $table) {
                $table->integer('conference_edition_id')->nullable()->after('id');
            });
        }

        // Alter the existing 'type' column safely
        DB::statement("
            ALTER TABLE `critical_emails` 
            CHANGE `type` `type` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the column if it exists
        if (Schema::hasColumn('critical_emails', 'conference_edition_id')) {
            Schema::table('critical_emails', function (Blueprint $table) {
                $table->dropColumn('conference_edition_id');
            });
        }

        // Optionally, revert 'type' column to previous definition if needed
        // DB::statement("ALTER TABLE `critical_emails` CHANGE `type` `type` ...previous definition...");
    }
};
