<?php

namespace Database\Seeders;

use App\Modules\Blog\BlogMedia;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use Throwable;

class BackfillBlogFeaturedCoversSeeder extends Seeder
{
    public function run(): void
    {
        $directory = resource_path('blog-covers');
        $manifest = json_decode(file_get_contents($directory.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
        $slugs = array_column($manifest, 'slug');
        if (count($slugs) !== count(array_unique($slugs))) {
            throw new RuntimeException('Blog cover manifest contains duplicate slugs.');
        }

        // Check every bundled asset before changing any post.
        foreach ($slugs as $slug) {
            if (!is_file($directory.'/'.$slug.'.png')) {
                throw new RuntimeException('Missing blog cover asset: '.$slug.'.png');
            }
        }

        $posts = BlogPost::whereIn('slug', $slugs)->get(['id', 'slug', 'featured_image'])->keyBy('slug');
        $updated = 0;
        $preserved = 0;
        $notFound = 0;

        foreach ($slugs as $slug) {
            $post = $posts->get($slug);
            if (!$post) {
                $notFound++;
                continue;
            }
            if (BlogMedia::url($post->featured_image)) {
                $preserved++;
                continue;
            }

            $temporary = tempnam(sys_get_temp_dir(), 'gsf-cover-');
            if ($temporary === false || !copy($directory.'/'.$slug.'.png', $temporary)) {
                if ($temporary !== false && is_file($temporary)) {
                    unlink($temporary);
                }
                throw new RuntimeException('Unable to stage blog cover: '.$slug);
            }

            $uploadedPath = null;
            try {
                $file = new UploadedFile($temporary, $slug.'.png', 'image/png', null, true);
                $uploadedPath = BlogMedia::upload($file, 'featured_image');
                $post->update(['featured_image' => $uploadedPath]);
                $updated++;
            } catch (Throwable $exception) {
                if ($uploadedPath && is_file(public_path($uploadedPath))) {
                    unlink(public_path($uploadedPath));
                }
                throw $exception;
            } finally {
                if (is_file($temporary)) {
                    unlink($temporary);
                }
            }
        }

        $this->command?->info("Blog covers: {$updated} updated, {$preserved} existing images preserved, {$notFound} posts absent.");
    }
}
