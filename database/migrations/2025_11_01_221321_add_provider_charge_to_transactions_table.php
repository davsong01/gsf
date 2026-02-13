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
        if (!Schema::hasColumn('transactions', 'payment_provider_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'payment_provider_id')) {
                    $table->integer('payment_provider_id')->nullable()->after('id');
                }
            });
        }

        if (!Schema::hasColumn('transactions', 'provider_charge')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'provider_charge')) {
                    $table->decimal('provider_charge', 10,2)->nullable()->after('id');
                }
            });
        }

        if (!Schema::hasColumn('transactions', 'total_amount')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->nullable()->after('id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'payment_provider_id')) {
                $table->dropColumn('payment_provider_id');
            }
            if (Schema::hasColumn('transactions', 'provider_charge')) {
                $table->dropColumn('provider_charge');
            }
            if (Schema::hasColumn('transactions', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
        });
    }
};
