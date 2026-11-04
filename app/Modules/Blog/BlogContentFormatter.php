<?php

namespace App\Modules\Blog;

class BlogContentFormatter
{
    public static function paragraphize(string $html): string
    {
        $html = str_replace(["\r\n", "\r"], "\n", $html);
        $html = preg_replace('/<strong\b[^>]*>\s*(?:&nbsp;|&#160;|&#xA0;|\xC2\xA0)\s*<\/strong>/i', '', $html) ?? $html;
        $html = preg_replace('/<p\b[^>]*>\s*(?:<strong\b[^>]*>\s*(?:&nbsp;|&#160;|&#xA0;|\xC2\xA0)?\s*<\/strong>\s*)*<\/p>/i', '', $html) ?? $html;

        if (preg_match('/<p\b/i', $html)) {
            return $html;
        }

        $chunks = preg_split('/\n\s*\n+/', trim($html), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $blocks = [];

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '' || preg_match('/^(?:&nbsp;|&#160;|\xC2\xA0|\s)+$/i', $chunk)) {
                continue;
            }

            if (preg_match('/^<(h[1-6]|div|blockquote|ul|ol|table|figure|hr)\b/i', $chunk)) {
                $blocks[] = $chunk;
            } else {
                $blocks[] = '<p>'.$chunk.'</p>';
            }
        }

        return implode("\n", $blocks);
    }
}
