<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('sex');
            }
        });

        // Copy values from 'sex' to 'gender' only if both columns exist
        if (Schema::hasColumn('users', 'sex') && Schema::hasColumn('users', 'gender')) {
            DB::statement("UPDATE users SET gender = sex");
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sex')) {
                $table->dropColumn('sex');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sex')) {
                $table->string('sex')->nullable()->after('gender');
            }
        });

        // Copy values back from 'gender' to 'sex'
        if (Schema::hasColumn('users', 'gender') && Schema::hasColumn('users', 'sex')) {
            DB::statement("UPDATE users SET sex = gender");
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
