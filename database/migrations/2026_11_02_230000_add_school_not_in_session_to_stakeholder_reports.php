<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stakeholder_reports', 'school_not_in_session')) {
            Schema::table('stakeholder_reports', function (Blueprint $table) {
                $table->boolean('school_not_in_session')->default(false)->after('edit_mode');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stakeholder_reports', 'school_not_in_session')) {
            Schema::table('stakeholder_reports', function (Blueprint $table) {
                $table->dropColumn('school_not_in_session');
            });
        }
    }
};
