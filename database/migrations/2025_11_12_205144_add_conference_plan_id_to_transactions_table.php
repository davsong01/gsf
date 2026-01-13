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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'conference_plan_id')) {
                $table->integer('conference_plan_id')->nullable()->after('conference_edition_id');
            }

            if (!Schema::hasColumn('transactions', 'conference_speakers')) {
                $table->json('conference_speakers')->nullable()->after('conference_plan_id');
            }

            if (!Schema::hasColumn('transactions', 'conference_faqs')) {
                $table->json('conference_faqs')->nullable()->after('conference_speakers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'conference_faqs')) {
                $table->dropColumn('conference_faqs');
            }

            if (Schema::hasColumn('transactions', 'conference_speakers')) {
                $table->dropColumn('conference_speakers');
            }

            if (Schema::hasColumn('transactions', 'conference_plan_id')) {
                $table->dropColumn('conference_plan_id');
            }
        });
    }
};
