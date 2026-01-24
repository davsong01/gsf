<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'is_graduated')) {
            return;
        };
        // 1. Add is_graduated column
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_graduated')->default(0)->after('status');
        });

        // 2. Copy status values into is_graduated
        DB::statement("UPDATE users SET is_graduated = status");

        // 3. Change status column to string
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->change();
        });

        DB::statement("UPDATE users SET status = 'active'");
    }

    public function down(): void
    {
        // revert status back to integer
        Schema::table('users', function (Blueprint $table) {
            $table->integer('status')->change();
        });

        // copy back values
        DB::statement("UPDATE users SET status = is_graduated");

        // drop is_graduated
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_graduated');
        });
    }
};
