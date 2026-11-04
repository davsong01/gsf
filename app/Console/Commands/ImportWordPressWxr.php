<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Blog\BlogMedia;
use App\Modules\Blog\BlogContentFormatter;
use Illuminate\Http\UploadedFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

class ImportWordPressWxr extends Command
{
    protected $signature = 'blog:import-wordpress {file : Path to a WordPress WXR export} {--dry-run : Preview without changing the database or downloading media} {--no-media : Keep media URLs as exported instead of downloading them}';
    protected $description = 'Import published WordPress posts, categories, tags, featured images, and legacy URLs from a WXR export.';

    private const WP = 'http://wordpress.org/export/1.2/';
    private const CONTENT = 'http://purl.org/rss/1.0/modules/content/';
    private const EXCERPT = 'http://wordpress.org/export/1.2/excerpt/';
    private array $downloaded = [];
    private array $failedDownloads = [];
    private array $attachmentUrls = [];
    private array $wpCategorySlugs = [];

    public function handle(): int
    {
        $file = $this->argument('file');
        if (!is_file($file) || !is_readable($file)) {
            $this->error("WXR file not found or unreadable: {$file}");
            return self::FAILURE;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
        if (!$xml || !isset($xml->channel)) {
            $this->error('The file is not a valid WordPress WXR export.');
            return self::FAILURE;
        }

        $channel = $xml->channel;
        $items = $channel->item;
        $this->buildCategoryMap($channel);
        $this->buildAttachmentMap($items);
        $posts = [];
        foreach ($items as $item) {
            $wp = $item->children(self::WP);
            if ((string) $wp->post_type !== 'post' || (string) $wp->status !== 'publish') {
                continue;
            }
            $posts[] = $item;
        }

        $this->info('Published posts found: '.count($posts));
        $this->info('Attachment records found: '.count($this->attachmentUrls));
        $this->info('Published items in trash/draft/private and other post types are excluded.');

        if ($this->option('dry-run')) {
            $this->table(['WordPress ID', 'Title', 'Slug', 'Category', 'Tags', 'Featured image'], array_map(
                fn (SimpleXMLElement $item) => $this->previewRow($item),
                $posts
            ));
            return self::SUCCESS;
        }

        $this->ensureMetadataColumns();
        $firstSuperAdmin = User::query()->where('role', 1)->orderBy('id')->first();
        if (!$firstSuperAdmin) {
            $this->error('Cannot import WordPress posts because no super admin account exists.');
            return self::FAILURE;
        }
        $counts = ['created' => 0, 'updated' => 0, 'media_downloaded' => 0, 'media_failed' => 0];
        foreach ($posts as $item) {
            $body = (string) $item->children(self::CONTENT)->encoded;
            if (!$this->option('no-media')) {
                preg_match_all('~https?://(?:www\.)?gsfnational\.org/wp-content/uploads/[^\s"\'<>]+~i', html_entity_decode($body, ENT_QUOTES | ENT_HTML5), $matches);
                foreach (array_unique($matches[0] ?? []) as $mediaUrl) {
                    if (isset($this->downloaded[$mediaUrl])) {
                        continue;
                    }
                    $path = $this->downloadMedia($mediaUrl);
                    if ($path) {
                        $this->downloaded[$mediaUrl] = $path;
                        $counts['media_downloaded']++;
                    } else {
                        $counts['media_failed']++;
                    }
                }
            }
            $attachmentId = $this->featuredAttachmentId($item);
            if (!$this->option('no-media') && $attachmentId && isset($this->attachmentUrls[$attachmentId]) && !isset($this->downloaded[$this->attachmentUrls[$attachmentId]])) {
                $path = $this->downloadMedia($this->attachmentUrls[$attachmentId]);
                if ($path) {
                    $this->downloaded[$this->attachmentUrls[$attachmentId]] = $path;
                    $counts['media_downloaded']++;
                } else {
                    $counts['media_failed']++;
                }
            }
        }

        foreach ($posts as $item) {
            $this->importPost($item, $firstSuperAdmin->id, $counts);
        }

        $this->newLine();
        $this->info("Import complete: {$counts['created']} created, {$counts['updated']} updated by WordPress ID.");
        $this->info("Featured media downloaded: {$counts['media_downloaded']}; failed or unavailable: {$counts['media_failed']}.");
        return self::SUCCESS;
    }

    private function buildCategoryMap(SimpleXMLElement $channel): void
    {
        foreach ($channel->children(self::WP)->category as $category) {
            $c = $category->children(self::WP);
            $this->wpCategorySlugs[(string) $c->term_id] = (string) $c->category_nicename;
        }
    }

    private function buildAttachmentMap(iterable $items): void
    {
        foreach ($items as $item) {
            $wp = $item->children(self::WP);
            if ((string) $wp->post_type === 'attachment') {
                $id = (string) $wp->post_id;
                $url = trim((string) $wp->attachment_url);
                if ($id !== '' && $url !== '') {
                    $this->attachmentUrls[$id] = $url;
                }
            }
        }
    }

    private function previewRow(SimpleXMLElement $item): array
    {
        $categories = $this->terms($item, 'category');
        $tags = $this->terms($item, 'post_tag');
        $featured = $this->featuredAttachmentId($item);
        return [
            (string) $item->children(self::WP)->post_id,
            Str::limit(trim((string) $item->title), 55),
            (string) $item->children(self::WP)->post_name,
            $categories[0]['name'] ?? '—',
            count($tags),
            $featured && isset($this->attachmentUrls[$featured]) ? 'yes' : 'no',
        ];
    }

    private function importPost(SimpleXMLElement $item, int $authorId, array &$counts): void
    {
        $wp = $item->children(self::WP);
        $wpId = (int) $wp->post_id;
        $title = trim((string) $item->title) ?: 'Untitled post';
        $slug = trim((string) $wp->post_name) ?: Str::slug($title);
        $terms = $this->terms($item, 'category');
        $tags = $this->terms($item, 'post_tag');
        $category = $this->primaryCategory($item, $terms);
        $publishedAt = $this->parseDate((string) $wp->post_date_gmt) ?? $this->parseDate((string) $wp->post_date) ?? now();
        $thumbnailId = $this->featuredAttachmentId($item);
        $thumbnailUrl = $thumbnailId ? ($this->attachmentUrls[$thumbnailId] ?? null) : null;
        $body = $this->sanitizeHtml($this->rewriteImages((string) $item->children(self::CONTENT)->encoded));
        $excerpt = $this->sanitizeHtml((string) $item->children(self::EXCERPT)->encoded);

        $existing = DB::table('blog_posts')->where('wordpress_id', $wpId)->first();
        if (!$existing) {
            $collision = DB::table('blog_posts')->where('slug', $slug)->exists();
            if ($collision) {
                $slug .= '-wp-'.$wpId;
            }
        } elseif (DB::table('blog_posts')->where('slug', $slug)->where('id', '<>', $existing->id)->exists()) {
            $slug .= '-wp-'.$wpId;
        }

        $categoryId = $category ? $this->categoryId($category) : null;
        $featuredPath = $thumbnailUrl ? ($this->downloaded[$thumbnailUrl] ?? $thumbnailUrl) : null;
        $data = [
            'type' => 'article',
            'title' => Str::limit($title, 255, ''),
            'slug' => Str::limit($slug, 255, ''),
            'excerpt' => $excerpt !== '' ? $excerpt : null,
            'body' => $body,
            'featured_image' => $featuredPath,
            'author_type' => User::class,
            'author_id' => $authorId,
            'legacy_author_name' => null,
            'category_id' => $categoryId,
            'status' => 'published',
            'published_at' => $publishedAt,
            'seo_title' => null,
            'seo_description' => null,
            'social_image' => $featuredPath,
            'canonical_url' => null,
            'scope_type' => 'national',
            'scope_id' => null,
            'allow_comments' => false,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('blog_posts')->where('id', $existing->id)->update($data);
            $postId = (int) $existing->id;
            $counts['updated']++;
        } else {
            $data['wordpress_id'] = $wpId;
            $data['created_at'] = now();
            $postId = (int) DB::table('blog_posts')->insertGetId($data);
            $counts['created']++;
        }

        $tagIds = [];
        foreach ($tags as $tag) {
            $tagIds[] = $this->tagId($tag);
        }
        DB::table('blog_post_tag')->where('post_id', $postId)->delete();
        foreach (array_unique($tagIds) as $tagId) {
            DB::table('blog_post_tag')->insertOrIgnore(['post_id' => $postId, 'tag_id' => $tagId]);
        }

        $oldUrl = trim((string) $item->link);
        $oldPath = parse_url($oldUrl, PHP_URL_PATH) ?: '';
        $oldPath = '/'.trim($oldPath, '/');
        if ($oldPath !== '/') {
            DB::table('blog_redirects')->updateOrInsert(
                ['from_path' => Str::limit($oldPath, 255, '')],
                ['to_path' => '/news/'.$data['slug'], 'status_code' => 301, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    private function terms(SimpleXMLElement $item, string $domain): array
    {
        $result = [];
        foreach ($item->category as $term) {
            if ((string) $term['domain'] === $domain) {
                $name = trim((string) $term);
                $slug = trim((string) $term['nicename']);
                if ($name !== '') {
                    $result[] = ['name' => $name, 'slug' => $slug ?: Str::slug($name)];
                }
            }
        }
        return $result;
    }

    private function primaryCategory(SimpleXMLElement $item, array $categories): ?array
    {
        if (!$categories) {
            return null;
        }
        $primaryId = null;
        foreach ($item->children(self::WP)->postmeta as $meta) {
            $m = $meta->children(self::WP);
            if ((string) $m->meta_key === '_yoast_wpseo_primary_category') {
                $primaryId = (string) $m->meta_value;
                break;
            }
        }
        if ($primaryId) {
            $primarySlug = $this->wpCategorySlugs[$primaryId] ?? null;
            foreach ($categories as $category) {
                if ($primarySlug && $category['slug'] === $primarySlug) {
                    return $category;
                }
            }
        }
        return $categories[0];
    }

    private function featuredAttachmentId(SimpleXMLElement $item): ?string
    {
        foreach ($item->children(self::WP)->postmeta as $meta) {
            $m = $meta->children(self::WP);
            if ((string) $m->meta_key === '_thumbnail_id') {
                return trim((string) $m->meta_value) ?: null;
            }
        }
        return null;
    }

    private function categoryId(array $term): int
    {
        return (int) DB::table('blog_categories')->where('slug', $term['slug'])->value('id')
            ?: (int) DB::table('blog_categories')->insertGetId(['name' => $term['name'], 'slug' => $this->uniqueTermSlug('blog_categories', $term['slug']), 'created_at' => now(), 'updated_at' => now()]);
    }

    private function tagId(array $term): int
    {
        return (int) DB::table('blog_tags')->where('slug', $term['slug'])->value('id')
            ?: (int) DB::table('blog_tags')->insertGetId(['name' => $term['name'], 'slug' => $this->uniqueTermSlug('blog_tags', $term['slug']), 'created_at' => now(), 'updated_at' => now()]);
    }

    private function uniqueTermSlug(string $table, string $slug): string
    {
        $base = Str::limit($slug ?: 'term', 175, '');
        $candidate = $base;
        for ($i = 2; DB::table($table)->where('slug', $candidate)->exists(); $i++) {
            $candidate = Str::limit($base, 170, '').'-'.$i;
        }
        return $candidate;
    }

    private function parseDate(string $value): mixed
    {
        if (!$value || str_starts_with($value, '0000-00-00')) {
            return null;
        }
        try {
            return \Illuminate\Support\Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function downloadMedia(string $url): ?string
    {
        if (isset($this->failedDownloads[$url])) {
            return null;
        }

        if (!preg_match('~^https?://(?:www\.)?gsfnational\.org(/wp-content/uploads/[^?#]+)~iu', $url, $matches)
            || !preg_match('/\.(jpe?g|png|gif|webp|avif)$/i', $matches[1])) {
            $this->failedDownloads[$url] = true;
            return null;
        }
        $path = $matches[1];

        $relative = 'uploads/blog/'.hash('sha256', $url).'-'.basename($path);
        if (is_file(public_path($relative))) {
            return $relative;
        }
        try {
            $response = Http::connectTimeout(3)->timeout(8)->withOptions(['allow_redirects' => false])->get($url);
            if (!$response->successful() || !$response->header('Content-Type') || !str_starts_with(strtolower($response->header('Content-Type')), 'image/')) {
                $this->failedDownloads[$url] = true;
                return null;
            }
            $bytes = $response->body();
            if (strlen($bytes) > 15 * 1024 * 1024 || !@getimagesizefromstring($bytes)) {
                $this->failedDownloads[$url] = true;
                return null;
            }
            $temporary = tempnam(sys_get_temp_dir(), 'blog-import-');
            if ($temporary === false || file_put_contents($temporary, $bytes) === false) {
                $this->failedDownloads[$url] = true;
                return null;
            }
            try {
                $file = new UploadedFile($temporary, basename($path), $response->header('Content-Type'), null, true);
                return BlogMedia::upload($file, '', basename($relative));
            } finally {
                if (is_file($temporary)) {
                    unlink($temporary);
                }
            }
        } catch (Throwable $e) {
            $this->failedDownloads[$url] = true;
            $this->warn('Media download failed: '.$url.' ('.$e->getMessage().')');
            return null;
        }
    }

    private function rewriteImages(string $html): string
    {
        return preg_replace_callback('/\b(src|href)=("|\')([^"\']+)("|\')/i', function ($matches) {
            $url = html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5);
            $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');
            $path = parse_url($url, PHP_URL_PATH) ?: '';
            if (in_array($host, ['gsfnational.org', 'www.gsfnational.org'], true)
                && str_starts_with($path, '/wp-content/uploads/')
                && isset($this->downloaded[$url])) {
                return $matches[1].'='.$matches[2].e(BlogMedia::url($this->downloaded[$url])).$matches[4];
            }
            return $matches[0];
        }, $html) ?? $html;
    }

    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('#<(script|style|object|embed|form|input|button|textarea|select|iframe|svg|math)\b[^>]*>.*?</\1\s*>#is', '', $html) ?? $html;
        $html = preg_replace('#<(script|style|object|embed|form|input|button|textarea|select|iframe|svg|math)\b[^>]*/?>#is', '', $html) ?? $html;
        $html = strip_tags($html, '<p><br><hr><h1><h2><h3><h4><h5><h6><blockquote><ul><ol><li><strong><b><em><i><u><s><del><ins><a><img><figure><figcaption><div><span><table><thead><tbody><tr><th><td><pre><code><sup><sub>');
        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/\s+(style|srcset|formaction|xlink:href)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace_callback('/\s(href|src)\s*=\s*("|\')([^"\']*)("|\')/i', function ($m) {
            $url = trim(html_entity_decode($m[3], ENT_QUOTES | ENT_HTML5));
            if (preg_match('/^(javascript|vbscript|data):/i', $url)) {
                return '';
            }
            return $m[0];
        }, $html) ?? $html;
        return BlogContentFormatter::paragraphize($html);
    }

    private function ensureMetadataColumns(): void
    {
        foreach (['wordpress_id', 'legacy_author_name'] as $column) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('blog_posts', $column)) {
                $this->error("Missing blog_posts.{$column}; run php artisan migrate first.");
                exit(self::FAILURE);
            }
        }
    }
}
