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
            $table->integer('conference_plan_id')->after('conference_edition_id')->nullable();
            $table->json('conference_speakers')->after('conference_edition_id')->nullable();
            $table->json('conference_faqs')->after('conference_edition_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('conference_plan_id');
            $table->dropColumn('conference_speakers');
            $table->dropColumn('conference_faqs');
        });
    }
};
