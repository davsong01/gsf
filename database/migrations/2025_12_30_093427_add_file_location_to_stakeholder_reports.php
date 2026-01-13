<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stakeholder_reports', 'file_location')) {
            Schema::table('stakeholder_reports', function (Blueprint $table) {
                $table->string('file_location')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('stakeholder_reports', 'file_location')) {
            Schema::table('stakeholder_reports', function (Blueprint $table) {
                $table->dropColumn('file_location');
            });
        }
    }
};
