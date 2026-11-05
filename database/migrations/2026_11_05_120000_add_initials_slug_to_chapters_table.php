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

        DB::table('chapters')->select('id', 'name')->orderBy('id')->chunkById(500, function ($chapters) {
            foreach ($chapters as $chapter) {
                $initials = collect(preg_split('/\s+/u', trim((string) $chapter->name), -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn ($word) => Str::substr($word, 0, 1))
                    ->implode('');

                DB::table('chapters')->where('id', $chapter->id)->update([
                    'slug' => Str::lower($initials),
                ]);
            }
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
