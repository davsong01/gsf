<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\BackfillChapterStatesSeeder;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('chapters', 'state')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->string('state', 40)->nullable()->after('address')->index();
            });
        }

        (new BackfillChapterStatesSeeder())->run();
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex(['state']);
            $table->dropColumn('state');
        });
    }
};
