<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('award_entries')) {
            Schema::rename(
                'award_entries',
                'award_entries_old'
            );
        }

        if (Schema::hasTable('award_entries2')) {
            Schema::rename(
                'award_entries2',
                'award_entries'
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('award_entries')) {
            Schema::rename(
                'award_entries',
                'award_entries2'
            );
        }

        if (Schema::hasTable('award_entries_old')) {
            Schema::rename(
                'award_entries_old',
                'award_entries'
            );
        }
    }
};
