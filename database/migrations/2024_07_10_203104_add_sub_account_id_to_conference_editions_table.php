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
        if (!Schema::hasColumn('conference_editions', 'enable_sub_account')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->string('enable_sub_account')->nullable();
            });
        }

        if (!Schema::hasColumn('conference_editions', 'paystack_subaccount_id')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->string('paystack_subaccount_id')->nullable();
            });
        }

        if (!Schema::hasColumn('conference_editions', 'conference_favicon')) {
            Schema::table('conference_editions', function (Blueprint $table) {
                $table->string('conference_favicon')->nullable();
            });
        }

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_editions', function (Blueprint $table) {
            $table->dropColumn("enable_sub_account");
            $table->dropColumn("paystack_subaccount_id");
            $table->dropColumn("conference_favicon");
        }); 
    }
};
