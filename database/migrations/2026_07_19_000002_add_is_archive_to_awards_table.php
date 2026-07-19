<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->boolean('is_archive')->default(false)->after('current_shortlist_stage_id');
        });

        if (Schema::hasColumn('awards', 'deleted_at')) {
            DB::table('awards')
                ->whereNotNull('deleted_at')
                ->update([
                    'is_archive' => true,
                    'deleted_at' => null,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->dropColumn('is_archive');
        });
    }
};
