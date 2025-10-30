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
        // Remove old columns if they exist
        Schema::table('settings', function (Blueprint $table) {
            $columnsToRemove = [
                'new_alumni_registration_fee',
                'alumni_registration_fee',
                'close_registration',
                'start_date',
                'end_date',
                'conference_theme',
                'conference_overview',
                'PAYSTACK_PUBLIC_KEY',
                'PAYSTACK_SECRET_KEY',
                'MERCHANT_EMAIL',
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Add new column
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'ministry')) {
                $table->string('ministry')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_editions', function (Blueprint $table) {
            if (Schema::hasColumn('conference_editions', 'ministry')) {
                $table->dropColumn('ministry');
            }

            // Recreate removed columns for rollback (optional)
            $table->decimal('new_alumni_registration_fee', 10, 2)->nullable();
            $table->decimal('alumni_registration_fee', 10, 2)->nullable();
            $table->boolean('close_registration')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('conference_theme')->nullable();
            $table->text('conference_overview')->nullable();
            $table->string('PAYSTACK_PUBLIC_KEY')->nullable();
            $table->string('PAYSTACK_SECRET_KEY')->nullable();
            $table->string('MERCHANT_EMAIL')->nullable();
        });
    }
};
