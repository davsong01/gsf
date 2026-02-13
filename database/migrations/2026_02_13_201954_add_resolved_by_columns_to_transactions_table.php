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
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('resolved_by')->nullable()->after('status');
            $table->tinyInteger('resolved_transaction_id')->default(0)->after('status');
            $table->timestamp('resolved_at')->nullable()->after('resolved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('resolved_by');
            $table->dropColumn('resolved_at');
            $table->dropColumn('resolved_transaction_id');
        });
    }
};
