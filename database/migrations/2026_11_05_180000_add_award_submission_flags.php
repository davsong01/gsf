<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('award_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('award_settings', 'go_submission_enabled')) {
                $table->boolean('go_submission_enabled')->default(true);
            }
            if (!Schema::hasColumn('award_settings', 'etf_submission_enabled')) {
                $table->boolean('etf_submission_enabled')->default(true);
            }
        });
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['go_submission_enabled', 'etf_submission_enabled'],
            fn ($column) => Schema::hasColumn('award_settings', $column)
        ));

        if ($columns) {
            Schema::table('award_settings', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};
