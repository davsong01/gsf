<?php

namespace Tests\Feature;

use App\Modules\Blog\BlogMedia;
use Database\Seeders\BackfillBlogFeaturedCoversSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BackfillBlogFeaturedCoversSeederTest extends TestCase
{
    public function test_seeder_fills_missing_cover_and_preserves_an_existing_upload_on_rerun(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('featured_image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        $preservedPath = BlogMedia::upload(UploadedFile::fake()->image('existing.png'), 'featured_image');
        $generatedPath = null;

        try {
            DB::table('blog_posts')->insert([
                ['slug' => 'head-of-youth-and-campus-ministries-visit-osun-field', 'featured_image' => null],
                ['slug' => 'gsf-national-inducts-new-leaders', 'featured_image' => $preservedPath],
            ]);

            (new BackfillBlogFeaturedCoversSeeder())->run();
            $generatedPath = DB::table('blog_posts')->where('slug', 'head-of-youth-and-campus-ministries-visit-osun-field')->value('featured_image');

            $this->assertStringStartsWith('uploads/blog/featured_image/', $generatedPath);
            $this->assertFileExists(public_path($generatedPath));
            $this->assertNotNull(BlogMedia::url($generatedPath));
            $this->assertSame($preservedPath, DB::table('blog_posts')->where('slug', 'gsf-national-inducts-new-leaders')->value('featured_image'));

            (new BackfillBlogFeaturedCoversSeeder())->run();
            $this->assertSame($generatedPath, DB::table('blog_posts')->where('slug', 'head-of-youth-and-campus-ministries-visit-osun-field')->value('featured_image'));
        } finally {
            foreach ([$preservedPath, $generatedPath] as $path) {
                if ($path && is_file(public_path($path))) {
                    unlink(public_path($path));
                }
            }
        }
    }
}
