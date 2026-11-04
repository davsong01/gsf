<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('blog_post_category')) {
            Schema::create('blog_post_category', function (Blueprint $table) {
                $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('blog_categories')->cascadeOnDelete();
                $table->primary(['post_id', 'category_id']);
                $table->index('category_id');
            });
        }

        if (Schema::hasColumn('blog_posts', 'category_id')) {
            DB::table('blog_posts')->whereNotNull('category_id')->orderBy('id')->chunkById(500, function ($posts) {
                foreach ($posts as $post) {
                    DB::table('blog_post_category')->insertOrIgnore([
                        'post_id' => $post->id,
                        'category_id' => $post->category_id,
                    ]);
                }
            });

            $categoryForeignKey = collect(Schema::getForeignKeys('blog_posts'))
                ->first(fn (array $foreignKey) => in_array('category_id', $foreignKey['columns'], true));

            Schema::table('blog_posts', function (Blueprint $table) use ($categoryForeignKey) {
                if ($categoryForeignKey) {
                    $table->dropForeign($categoryForeignKey['name']);
                }
                $table->dropColumn('category_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('blog_posts', 'category_id')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            });
            DB::table('blog_post_category')->orderBy('post_id')->get()->groupBy('post_id')->each(function ($assignments) {
                DB::table('blog_posts')->where('id', $assignments->first()->post_id)
                    ->update(['category_id' => $assignments->first()->category_id]);
            });
        }

        Schema::dropIfExists('blog_post_category');
    }
};
