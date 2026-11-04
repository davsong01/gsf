<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'motto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('motto', 500)->nullable()->after('skills');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'motto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('motto');
            });
        }
    }
};
