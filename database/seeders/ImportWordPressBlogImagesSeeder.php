<?php

namespace Database\Seeders;

use App\Modules\Blog\BlogMedia;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class ImportWordPressBlogImagesSeeder extends Seeder
{
    private array $resolved = [];
    private array $missing = [];
    private int $copied = 0;

    public function run(): void
    {
        $this->relocateExistingFiles();

        $source = realpath(config('blog.wordpress_uploads_path'));
        if (!$source || !is_dir($source)) {
            throw new RuntimeException('WordPress uploads folder was not found. Set BLOG_WORDPRESS_UPLOADS_PATH to its location.');
        }

        $updated = 0;
        foreach (BlogPost::whereNotNull('wordpress_id')->cursor() as $post) {
            $changes = [];

            foreach (['featured_image', 'social_image'] as $field) {
                $replacement = $this->localUrl($post->{$field}, $source);
                if ($replacement) {
                    $changes[$field] = $replacement;
                }
            }

            $body = preg_replace_callback(
                '~https?://(?:www\.)?gsfnational\.org/wp-content/uploads/[^\s"\'<>]+~i',
                function (array $match) use ($source): string {
                    $url = html_entity_decode($match[0], ENT_QUOTES | ENT_HTML5);
                    $replacement = $this->localUrl($url, $source);

                    return $replacement ? BlogMedia::url($replacement) : $match[0];
                },
                (string) $post->body
            );
            if ($body !== null && $body !== $post->body) {
                $changes['body'] = $body;
            }

            if ($changes) {
                $post->update($changes);
                $updated++;
            }
        }

        $this->command?->info("Updated {$updated} posts; copied {$this->copied} files to public/uploads/blog.");
        $this->command?->warn(count($this->missing).' referenced files were absent from the downloaded folder.');
        foreach (array_slice(array_keys($this->missing), 0, 20) as $url) {
            $this->command?->line('Missing: '.$url);
        }
    }

    private function localUrl(?string $url, string $source): ?string
    {
        if (!$url || isset($this->missing[$url])) {
            return null;
        }
        if (isset($this->resolved[$url])) {
            return $this->resolved[$url];
        }

        if (!preg_match('~^https?://(?:www\.)?gsfnational\.org(/wp-content/uploads/[^?#]+)~iu', $url, $matches)) {
            return null;
        }

        $relative = rawurldecode(substr($matches[1], strlen('/wp-content/uploads/')));
        $extension = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'pdf', 'mp3'], true)) {
            return null;
        }

        $file = $this->sourceFile($source, $relative);
        if (!$file) {
            $this->missing[$url] = true;
            return null;
        }

        $destination = 'uploads/blog/'.hash('sha256', $url).'-'.basename($relative);
        if (!is_file(public_path($destination))) {
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'], true) && !getimagesize($file)) {
                $this->missing[$url] = true;
                return null;
            }
            if ($extension === 'pdf' && file_get_contents($file, false, null, 0, 5) !== '%PDF-') {
                $this->missing[$url] = true;
                return null;
            }

            $temporary = tempnam(sys_get_temp_dir(), 'blog-image-');
            if ($temporary === false || !copy($file, $temporary)) {
                throw new RuntimeException('Could not stage WordPress image: '.$file);
            }
            try {
                $upload = new UploadedFile($temporary, basename($relative), mime_content_type($file) ?: null, null, true);
                $destination = BlogMedia::upload($upload, '', basename($destination));
                $this->copied++;
            } finally {
                if (is_file($temporary)) {
                    unlink($temporary);
                }
            }
        }

        return $this->resolved[$url] = $destination;
    }

    private function relocateExistingFiles(): void
    {
        $oldDirectory = public_path('uploads/blog/wordpress-import');
        if (is_dir($oldDirectory)) {
            foreach (scandir($oldDirectory) ?: [] as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $old = $oldDirectory.'/'.$entry;
                $new = public_path('uploads/blog/'.$entry);
                if (!is_file($old) || (is_file($new) && hash_file('sha256', $old) !== hash_file('sha256', $new))) {
                    throw new RuntimeException('Cannot safely relocate imported file: '.$old);
                }
                if (!is_file($new) && !rename($old, $new)) {
                    throw new RuntimeException('Could not relocate imported file: '.$old);
                }
                if (is_file($old)) {
                    unlink($old);
                }
            }
            rmdir($oldDirectory);
        }

        foreach (BlogPost::whereNotNull('wordpress_id')->cursor() as $post) {
            $changes = [];
            foreach (['featured_image', 'social_image', 'body'] as $field) {
                if (str_contains((string) $post->{$field}, 'uploads/blog/wordpress-import/')) {
                    $changes[$field] = str_replace('uploads/blog/wordpress-import/', 'uploads/blog/', $post->{$field});
                }
            }
            if ($changes) {
                $post->update($changes);
            }
        }
    }

    private function sourceFile(string $source, string $relative): ?string
    {
        $folder = realpath($source.'/'.dirname($relative));
        if (!$folder || !str_starts_with($folder, $source.DIRECTORY_SEPARATOR)) {
            return null;
        }

        $name = basename($relative);
        $names = [$name];
        if (preg_match('/^(.+)-\d+x\d+(\.[^.]+)$/i', $name, $parts)) {
            $names[] = $parts[1].$parts[2];
        }

        foreach ($names as $candidate) {
            $exact = realpath($folder.'/'.$candidate);
            if ($exact && str_starts_with($exact, $source.DIRECTORY_SEPARATOR) && is_file($exact)) {
                return $exact;
            }
        }

        foreach (scandir($folder) ?: [] as $entry) {
            foreach ($names as $candidate) {
                if (strcasecmp($entry, $candidate) === 0) {
                    $file = realpath($folder.'/'.$entry);
                    if ($file && str_starts_with($file, $source.DIRECTORY_SEPARATOR) && is_file($file)) {
                        return $file;
                    }
                }
            }
        }

        return null;
    }
}
