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
        if (!Schema::hasColumn('conference_editions', 'hostel_assignment_type')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->string('hostel_assignment_type')->nullable();
            });
        }

        if (!Schema::hasColumn('conference_editions', 'service_point_assignment_type')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->string('service_point_assignment_type')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_editions', function (Blueprint $table) {
            $table->dropColumn("hostel_assignment_type");
            $table->dropColumn("service_point_assignment_type");
        }); 
    }
};
