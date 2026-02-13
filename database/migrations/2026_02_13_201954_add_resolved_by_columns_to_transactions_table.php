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
            $table->string('old_transid')->default(0)->after('transid');
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
            $table->dropColumn('old_trans_id');
        });
    }
};
