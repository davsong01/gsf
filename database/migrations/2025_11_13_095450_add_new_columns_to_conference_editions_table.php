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
        if (!Schema::hasColumn('conference_editions', 'faq_section_status')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->json('faq_section_status')->default(0)->after('ministry_id');
            });
        }

        if (!Schema::hasColumn('conference_editions', 'speaker_section_status')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->json('speaker_section_status')->default(0)->after('ministry_id');
            });
        }

        if (!Schema::hasColumn('conference_editions', 'faq_ids')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->json('faq_ids')->nullable()->after('ministry_id');
            });
        }

        if (!Schema::hasColumn('conference_editions', 'speaker_ids')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->json('speaker_ids')->nullable()->after('ministry_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_editions', function (Blueprint $table) {
            $table->dropColumn('faq_ids');
            $table->dropColumn('speaker_ids');
        });
    }
};
