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
        foreach (['stakeholder_question_sections', 'stakeholder_question_sub_sections'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'slug')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->string('slug')->nullable()->after('name');
                });
            }
        }

        $this->backfillSectionSlugs();
        $this->backfillSubSectionSlugs();

        foreach ([
            'stakeholder_question_sections',
            'stakeholder_question_sub_sections',
        ] as $table) {
            if (Schema::hasTable($table) && ! $this->hasIndex($table, $table . '_module_type_slug_unique')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->unique(['module_type', 'slug']);
                });
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'stakeholder_question_sections',
            'stakeholder_question_sub_sections',
        ] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'slug')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->dropUnique(['module_type', 'slug']);
                    $tableBlueprint->dropColumn('slug');
                });
            }
        }
    }

    protected function backfillSectionSlugs(): void
    {
        if (! Schema::hasTable('stakeholder_question_sections')) {
            return;
        }

        DB::table('stakeholder_question_sections')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $slug = $this->uniqueSlug(
                        'stakeholder_question_sections',
                        Str::slug($row->name),
                        $row->module_type ?? 'report',
                        $row->id
                    );

                    DB::table('stakeholder_question_sections')
                        ->where('id', $row->id)
                        ->update(['slug' => $slug]);
                }
            });
    }

    protected function backfillSubSectionSlugs(): void
    {
        if (! Schema::hasTable('stakeholder_question_sub_sections')) {
            return;
        }

        DB::table('stakeholder_question_sub_sections')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $sectionSlug = DB::table('stakeholder_question_sections')
                        ->where('id', $row->section_id)
                        ->value('slug');

                    $base = trim(($sectionSlug ?: '') . ' ' . $row->name);
                    $slug = $this->uniqueSlug(
                        'stakeholder_question_sub_sections',
                        Str::slug($base),
                        $row->module_type ?? 'report',
                        $row->id
                    );

                    DB::table('stakeholder_question_sub_sections')
                        ->where('id', $row->id)
                        ->update(['slug' => $slug]);
                }
            });
    }

    protected function uniqueSlug(string $table, string $baseSlug, string $moduleType, ?int $ignoreId = null): string
    {
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'item';
        $slug = $baseSlug;
        $counter = 2;

        while (
            DB::table($table)
                ->where('module_type', $moduleType)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    protected function hasIndex(string $table, string $indexName): bool
    {
        return ! empty(DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$indexName]));
    }

};
