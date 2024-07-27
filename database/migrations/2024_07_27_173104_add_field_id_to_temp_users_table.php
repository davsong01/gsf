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
        if (!Schema::hasColumn('temp_users', 'field_id')) {
            Schema::table('temp_users', function (Blueprint $table) {
                $table->string('field_id')->nullable()->after('chapter_id');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_users', function (Blueprint $table) {
            $table->dropColumn("field_id");
        }); 
    }
};
