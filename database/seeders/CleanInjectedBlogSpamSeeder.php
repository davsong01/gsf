<?php

namespace Database\Seeders;

use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Seeder;

class CleanInjectedBlogSpamSeeder extends Seeder
{
    private const SPAM_LINK = 'GSF_REMOVED_SPAM_LINK_71F2';

    public function run(): void
    {
        $changed = 0;
        $removed = 0;

        BlogPost::query()->whereNotNull('wordpress_id')->chunkById(100, function ($posts) use (&$changed, &$removed): void {
            foreach ($posts as $post) {
                // This imported article is unrelated to GSF and is almost entirely casino spam.
                if ($post->title === 'What Counties in Georgia Exempt Seniors from School Taxes California Learning Resource Network'
                    && str_contains((string) $post->body, 'Chicken Road 2')) {
                    $post->delete();
                    $removed++;
                    continue;
                }

                $clean = self::clean((string) $post->body);
                if ($clean === $post->body) {
                    continue;
                }

                $post->body = $clean;
                $post->save();
                $changed++;
                $this->command?->line("Cleaned blog post {$post->id}: {$post->title}");
            }
        });

        $this->command?->info("Blog spam cleanup: {$changed} posts cleaned, {$removed} spam-only posts removed.");
    }

    public static function clean(string $html): string
    {
        $original = $html;
        // Replace only links whose visible label advertises counterfeit watches.
        // Keeping a marker until the surrounding sentence is removed lets us handle
        // ads inserted in the middle of an otherwise valid paragraph.
        $html = preg_replace_callback('~<a\b[^>]*>.*?</a>~isu', static function (array $match): string {
            $label = html_entity_decode(strip_tags($match[0]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return preg_match('~replica|replik|rolex|fake watch|clone watch|cheap watch|orologi di alta qualità~iu', $label)
                ? self::SPAM_LINK : $match[0];
        }, $html);

        // Verified ad copy found in the imported WordPress articles. These spans
        // contain no article prose; the short link markers below are removed later.
        foreach ([
            '~Wir verkaufen hauptsächlich Schweizer.*?Audemars Piguet\.\s*~isu',
            '~Buy AAA Best cheap.*?Watches For Sale\.\s*~isu',
            '~Buy perfect aaa.*?wholesale shop\.\s*~isu',
            '~Fast shipping and quality guarantee\..*?wisdom choice\.\s*~isu',
            '~If you like forever classic fake watches,.*?USA\.\s*~isu',
            '~High-quality fake breitling watches.*?online stores\.\s*~isu',
            '~If you like forever classic and elegant wrist.*?online shop\.\s*~isu',
            '~UK automatic.*?All the collections are available\.\s*~isu',
            '~US best 1:1 cheap rolex replica watches online store:.*?'.self::SPAM_LINK.'\s*~isu',
            '~Cheap 1:1 AAA Swiss Super Clone.*?For Men And Ladies\.\s*~isu',
        ] as $pattern) {
            // Never cross an HTML element boundary, apart from the link marker.
            $html = preg_replace_callback($pattern, static function (array $match): string {
                return str_contains($match[0], '<') ? $match[0] : '';
            }, $html);
        }

        if ($html === $original) {
            return $original;
        }

        $html = str_replace(self::SPAM_LINK, '', $html);
        // Remove empty paragraphs left by standalone ads and extra whitespace
        // before punctuation. Preserve the rest of the imported HTML verbatim.
        $html = preg_replace('~<p\b[^>]*>(?:\s|&nbsp;|\x{00a0})*</p>~iu', '', $html);
        $html = preg_replace('~[ \x{00a0}]+([.,;:!?])~u', '$1', $html);
        return $html;
    }
}
