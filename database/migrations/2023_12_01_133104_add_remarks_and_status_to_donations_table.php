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
        if (!Schema::hasColumn('donations', 'remarks')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->text('remarks')->nullable();
            });
        }

        if (!Schema::hasColumn('donations', 'status')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->string('status')->nullable();
            });
        }

        if (!Schema::hasColumn('donations', 'campus')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->integer('campus')->nullable();
            });
        }

        if (!Schema::hasColumn('donations', 'membership_status')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->string('membership_status')->nullable();
            });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('donations', 'remarks')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn("remarks");
            });
        }

        if (Schema::hasColumn('donations', 'status')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn("status");
            });
        }

        if (Schema::hasColumn('donations', 'campus')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn("campus");
            }); 
        }

        if (Schema::hasColumn('donations', 'membership_status')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn("membership_status");
            });
        }
    }
};
