<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('temp_members')) {
            return;
        }

        $hasSex = Schema::hasColumn('temp_members', 'sex');
        $hasGender = Schema::hasColumn('temp_members', 'gender');

        if ($hasSex && !$hasGender) {
            Schema::table('temp_members', function (Blueprint $table) {
                $table->renameColumn('sex', 'gender');
            });
        } elseif ($hasSex && $hasGender) {
            DB::table('temp_members')->whereNull('gender')->update([
                'gender' => DB::raw('sex'),
            ]);

            Schema::table('temp_members', function (Blueprint $table) {
                $table->dropColumn('sex');
            });
        } elseif (!$hasGender) {
            Schema::table('temp_members', function (Blueprint $table) {
                $table->string('gender')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('temp_members') || !Schema::hasColumn('temp_members', 'gender')) {
            return;
        }

        if (!Schema::hasColumn('temp_members', 'sex')) {
            Schema::table('temp_members', function (Blueprint $table) {
                $table->renameColumn('gender', 'sex');
            });
        }
    }
};
