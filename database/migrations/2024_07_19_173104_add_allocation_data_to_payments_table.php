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
        if (!Schema::hasColumn('payments', 'hostel_allocation_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('hostel_allocation_number')->nullable()->after('hostel_id');
            });
        }

        if (!Schema::hasColumn('payments', 'hostel_allocation_type')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('hostel_allocation_type')->nullable()->after('hostel_id');
            });
        }

        if (!Schema::hasColumn('payments', 'service_point_allocation_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('service_point_allocation_number')->nullable()->after('food_id');
            });
        }

        if (!Schema::hasColumn('payments', 'service_point_allocation_type')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('service_point_allocation_type')->nullable()->after('food_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn("chapter_ids");
            $table->dropColumn("field_ids");
        }); 
    }
};
