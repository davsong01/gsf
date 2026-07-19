<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('award_shortlist_stages', function (Blueprint $table) {
            $table->string('award_type')->nullable()->after('slug');
            $table->string('stage_engine')->default('manual')->after('award_type');
            $table->json('system_conditions')->nullable()->after('stage_engine');
        });
    }

    public function down(): void
    {
        Schema::table('award_shortlist_stages', function (Blueprint $table) {
            $table->dropColumn([
                'award_type',
                'stage_engine',
                'system_conditions',
            ]);
        });
    }
};
