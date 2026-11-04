<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Blog\BlogContentFormatter;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class FormatImportedWordPressBlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        $posts = BlogPost::query()->whereNotNull('wordpress_id');
        $needsAuthor = (clone $posts)->where(function (Builder $query) {
            $query->whereNull('author_type')->orWhereNull('author_id')->orWhereNotNull('legacy_author_name');
        })->exists();
        $firstSuperAdmin = $needsAuthor ? User::query()->where('role', 1)->orderBy('id')->first() : null;
        if ($needsAuthor && !$firstSuperAdmin) {
            throw new RuntimeException('Cannot assign imported blog posts: no super admin account exists.');
        }

        $updated = 0;
        $excerptsUpdated = 0;
        $authorsUpdated = 0;
        foreach ($posts->cursor() as $post) {
            $body = BlogContentFormatter::paragraphize((string) $post->body);
            $changes = [];
            if ($body !== $post->body) {
                $changes['body'] = $body;
                $updated++;
            }
            if (trim((string) $post->excerpt) === '') {
                $excerpt = $this->excerptFromContent($body);
                if ($excerpt !== '') {
                    $changes['excerpt'] = $excerpt;
                    $excerptsUpdated++;
                }
            }
            if ($firstSuperAdmin && (!$post->author_type || !$post->author_id || $post->legacy_author_name !== null)) {
                $changes['author_type'] = User::class;
                $changes['author_id'] = $firstSuperAdmin->id;
                $changes['legacy_author_name'] = null;
                $authorsUpdated++;
            }
            if ($changes) $post->update($changes);
        }

        $this->command?->info("Added paragraph spacing to {$updated} imported posts.");
        $this->command?->info("Generated excerpts for {$excerptsUpdated} imported posts.");
        $this->command?->info("Assigned {$authorsUpdated} imported posts to the first super admin.");
    }

    private function excerptFromContent(string $body): string
    {
        $text = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return Str::limit(trim($text), 300);
    }
}
