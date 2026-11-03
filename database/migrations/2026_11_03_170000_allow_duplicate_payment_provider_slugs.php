<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        $hasUniqueSlugIndex = false;

        if ($driver === 'mysql') {
            $hasUniqueSlugIndex = collect(DB::select('SHOW INDEX FROM payment_providers'))
                ->contains(fn ($index) => $index->Key_name === 'payment_providers_slug_unique' && (int) $index->Non_unique === 0);
        } elseif ($driver === 'sqlite') {
            $hasUniqueSlugIndex = collect(DB::select("PRAGMA index_list('payment_providers')"))
                ->contains(fn ($index) => $index->name === 'payment_providers_slug_unique' && (int) $index->unique === 1);
        }

        if ($hasUniqueSlugIndex) {
            Schema::table('payment_providers', function (Blueprint $table) {
                $table->dropUnique('payment_providers_slug_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('payment_providers', function (Blueprint $table) {
            $table->dropIndex('payment_providers_slug_index');
            $table->unique('slug', 'payment_providers_slug_unique');
        });
    }
};
