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
        Schema::table('payment_providers', function (Blueprint $table) {
            $table->boolean('enable_sub_account')
                ->default(false)
                ->after('status');

            $table->string('sub_account_code')
                ->nullable()
                ->after('enable_sub_account');

            $table->decimal('sub_account_fee_percentage', 5, 2)
                ->nullable()
                ->after('sub_account_code')
                ->comment('Percentage of total amount that goes to subaccount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_providers', function (Blueprint $table) {
            $table->dropColumn(['enable_sub_account', 'sub_account_code', 'sub_account_fee_percentage']);
        });
    }
};
