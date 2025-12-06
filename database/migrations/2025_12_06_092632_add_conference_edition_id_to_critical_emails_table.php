<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('critical_emails', function (Blueprint $table) {
            $table->integer('conference_edition_id')->after('id')->nullable();
        });

        // Use a raw statement to alter the existing `type` column collation/definition.
        // If you prefer a fluent change, ensure `doctrine/dbal` is installed and use the ->change() method.
        DB::statement("ALTER TABLE `critical_emails` CHANGE `type` `type` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the added column if it exists
        if (Schema::hasColumn('critical_emails', 'conference_edition_id')) {
            Schema::table('critical_emails', function (Blueprint $table) {
                $table->dropColumn('conference_edition_id');
            });
        }
    }
};
