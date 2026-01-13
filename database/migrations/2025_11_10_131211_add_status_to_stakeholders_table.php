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
        if (!Schema::hasColumn('stakeholders', 'status')) {
            Schema::table('stakeholders', function (Blueprint $table) {
                $table->string('status')->default('inactive')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stakeholders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
