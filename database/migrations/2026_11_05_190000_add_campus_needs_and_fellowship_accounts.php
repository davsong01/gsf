<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            foreach (['fellowship_bank_name', 'fellowship_account_name', 'fellowship_account_number'] as $column) {
                if (!Schema::hasColumn('chapters', $column)) {
                    $table->string($column, $column === 'fellowship_account_name' ? 160 : 120)->nullable();
                }
            }
        });

        if (!Schema::hasTable('chapter_needs')) {
            Schema::create('chapter_needs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
                $table->string('title', 160);
                $table->text('description');
                $table->decimal('amount', 12, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['chapter_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_needs');
        foreach (['fellowship_bank_name', 'fellowship_account_name', 'fellowship_account_number'] as $column) {
            if (Schema::hasColumn('chapters', $column)) {
                Schema::table('chapters', fn (Blueprint $table) => $table->dropColumn($column));
            }
        }
    }
};
