<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('stakeholder_designations')) {
            return;
        }

        if (! Schema::hasColumn('stakeholder_designations', 'slug')) {
            Schema::table('stakeholder_designations', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('name');
            });
        } else {
            // Keep the unique index within MySQL's 1000-byte key limit under utf8mb4.
            DB::statement('ALTER TABLE stakeholder_designations MODIFY slug VARCHAR(191) NULL');
        }

        DB::table('stakeholder_designations')
            ->orderBy('id')
            ->chunkById(500, function ($designations) {
                foreach ($designations as $designation) {
                    $baseSlug = Str::slug($designation->name) ?: 'designation';
                    $slug = $baseSlug;
                    $counter = 2;

                    while (DB::table('stakeholder_designations')
                        ->where('slug', $slug)
                        ->where('id', '!=', $designation->id)
                        ->exists()) {
                        $slug = $baseSlug . '-' . $counter++;
                    }

                    DB::table('stakeholder_designations')
                        ->where('id', $designation->id)
                        ->update(['slug' => $slug]);
                }
            });

        Schema::table('stakeholder_designations', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('stakeholder_designations')
            && Schema::hasColumn('stakeholder_designations', 'slug')) {
            Schema::table('stakeholder_designations', function (Blueprint $table) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            });
        }
    }
};
