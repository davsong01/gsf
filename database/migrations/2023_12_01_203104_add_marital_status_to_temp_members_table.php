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
        if (!Schema::hasColumn('temp_members', 'marital_status')) {
            Schema::table('temp_members', function (Blueprint $table) {
                if (!Schema::hasColumn('temp_members', 'marital_status')) {
                    $table->string('marital_status')->nullable();
                }
            });
        }

        if (!Schema::hasColumn('temp_members', 'date_of_birth')) {
            Schema::table('temp_members', function (Blueprint $table) {
                if (!Schema::hasColumn('temp_members', 'date_of_birth')) {
                    $table->string('date_of_birth')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_members', function (Blueprint $table) {
            if (Schema::hasColumn('temp_members', 'marital_status')) {
                $table->dropColumn("marital_status");
            }
            if (Schema::hasColumn('temp_members', 'date_of_birth')) {
                $table->dropColumn("date_of_birth");
            }
        });
    }
};
