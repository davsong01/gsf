<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old transactions table if it exists
        // if (Schema::hasTable('transactions')) {
        //     Schema::dropIfExists('transactions');
        // }

        // if (Schema::hasTable('temp_users')) {
        //     Schema::dropIfExists('temp_users');
        // }

        // // Rename payments table to transactions
        // if (Schema::hasTable('payments')) {
        //     Schema::rename('payments', 'transactions');
        // }

        // Add missing columns
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->string('status', 20)->nullable()->after('registration_status');
            }

            if (!Schema::hasColumn('transactions', 'provider_charge')) {
                $table->decimal('provider_charge', 10, 2)->nullable()->after('amount_paid');
            }
            if (!Schema::hasColumn('transactions', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('provider_charge');
            }
            if (!Schema::hasColumn('transactions', 'remarks')) {
                $table->text('remarks')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('transactions', 'fix_status')) {
                $table->string('fix_status')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('transactions', 'name')) {
                $table->string('name')->nullable()->after('fix_status');
            }
            if (!Schema::hasColumn('transactions', 'phone')) {
                $table->string('phone')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('transactions', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('transactions', 'gender')) {
                $table->string('gender', 20)->nullable()->after('email');
            }

            DB::statement("ALTER TABLE `transactions` CHANGE `user_id` `user_id` BIGINT(20) UNSIGNED NULL;");
        });
    }

    public function down(): void
    {
        // Rename back to payments if it exists
        if (Schema::hasTable('transactions')) {
            Schema::rename('transactions', 'temp_users');
        }
    }
};
