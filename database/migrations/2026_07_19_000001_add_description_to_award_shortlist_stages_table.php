<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('award_shortlist_stages', function (Blueprint $table) {
            $table->string('description')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('award_shortlist_stages', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
