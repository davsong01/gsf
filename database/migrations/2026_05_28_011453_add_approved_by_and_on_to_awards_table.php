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
        Schema::table('awards', function (Blueprint $table) {
            $table->integer('national_approved_by')->nullable()->after('national_status');
            $table->timestamp('national_approved_on')->nullable()->after('national_status');

            $table->integer('national_rejected_by')->nullable()->after('national_status');
            $table->timestamp('national_rejected_on')->nullable()->after('national_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->dropColumn('national_approved_by');
            $table->dropColumn('national_approved_on');
            $table->dropColumn('national_rejected_by');
            $table->dropColumn('national_rejected_on');
        });
    }
};
