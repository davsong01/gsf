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
        if (!Schema::hasColumn('food', 'field_ids')) {
            Schema::table('food', function (Blueprint $table) {
                $table->json('field_ids')->nullable()->after('level');
            });
        }

        if (!Schema::hasColumn('food', 'chapter_ids')) {
            Schema::table('food', function (Blueprint $table) {
                $table->json('chapter_ids')->nullable()->before('field_ids');
            });
        }   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food', function (Blueprint $table) {
            $table->dropColumn("field_ids");
            $table->dropColumn("chapter_ids");
        }); 
    }
};
