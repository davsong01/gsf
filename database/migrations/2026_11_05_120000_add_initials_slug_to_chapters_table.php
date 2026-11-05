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
        Schema::table('chapters', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        $usedSlugs = [];

        DB::table('chapters')->select('id', 'name')->orderBy('id')->chunkById(500, function ($chapters) use (&$usedSlugs) {
            foreach ($chapters as $chapter) {
                $name = preg_replace('/[^\pL\pN\s]/u', '', (string) $chapter->name) ?? (string) $chapter->name;
                $baseSlug = Str::lower(collect(preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn ($word) => Str::substr($word, 0, 1))
                    ->implode(''));
                $baseSlug = $baseSlug ?: 'chapter';
                $slug = $baseSlug;
                $suffix = 2;
                while (isset($usedSlugs[$slug])) {
                    $slug = $baseSlug . '-' . $suffix++;
                }
                $usedSlugs[$slug] = true;

                DB::table('chapters')->where('id', $chapter->id)->update([
                    'slug' => $slug,
                ]);
            }
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
