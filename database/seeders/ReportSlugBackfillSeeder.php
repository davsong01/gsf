<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportSlugBackfillSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->backfillSectionSlugs();
            $this->backfillSubSectionSlugs();
        });
    }

    protected function backfillSectionSlugs(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('stakeholder_question_sections')) {
            return;
        }

        DB::table('stakeholder_question_sections')
            ->where('module_type', 'report')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $slug = $this->uniqueSlug(
                        'stakeholder_question_sections',
                        Str::slug($row->name),
                        'report',
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
        if (! DB::getSchemaBuilder()->hasTable('stakeholder_question_sub_sections')) {
            return;
        }

        DB::table('stakeholder_question_sub_sections')
            ->where('module_type', 'report')
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
                        'report',
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
}
