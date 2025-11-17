<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('sex');
        });

        DB::statement("UPDATE users SET gender = sex");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sex');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sex')->nullable()->after('gender');
        });

        DB::statement("UPDATE users SET sex = gender");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
