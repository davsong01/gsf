<?php

namespace App\Modules\Blog;

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class BlogMedia
{
    public static function upload(UploadedFile $file, string $folder, ?string $filename = null): string
    {
        $name = $filename ?: Str::uuid().'.'.$file->extension();
        $directory = 'blog'.($folder !== '' ? '/'.trim($folder, '/') : '');
        app(FileUploadService::class)->publicUpload($file, $directory, '', $name);

        return 'uploads/'.$directory.'/'.$name;
    }

    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $mediaRoutePath = parse_url(route('blog.media'), PHP_URL_PATH);
        if (parse_url($path, PHP_URL_PATH) === $mediaRoutePath) {
            parse_str((string) parse_url($path, PHP_URL_QUERY), $query);
            $path = $query['path'] ?? '';
        }

        if (preg_match('~^(?:https?:)?//[^/]+(/[^?#]*)~i', $path, $matches)) {
            $path = $matches[1];
        }

        $path = rawurldecode((string) preg_replace('/[?#].*$/', '', $path));
        $path = ltrim($path, '/');
        if ($path === '' || preg_match('~(^|/)\.\.(/|$)~', $path)) {
            return null;
        }

        $publicPath = Str::startsWith($path, ['uploads/', 'storage/']) ? $path : 'storage/'.$path;

        if (!is_file(public_path($publicPath))) {
            return null;
        }

        return Str::startsWith($publicPath, 'uploads/blog/')
            ? route('blog.media', ['path' => $publicPath])
            : asset($publicPath);
    }

    public static function htmlWithAvailableImages(string $html): string
    {
        $html = preg_replace_callback('/<img\b[^>]*>/i', function (array $match): string {
            if (!preg_match('/\bsrc\s*=\s*(?:(["\'])(.*?)\1|([^\s>]+))/i', $match[0], $srcMatch)) {
                return '';
            }

            $source = html_entity_decode($srcMatch[2] !== '' ? $srcMatch[2] : ($srcMatch[3] ?? ''), ENT_QUOTES | ENT_HTML5);
            $localUrl = self::url($source);
            if (!$localUrl) {
                return '';
            }

            $attribute = $srcMatch[0];
            $replacement = 'src="'.e($localUrl).'"';

            return str_replace($attribute, $replacement, $match[0]);
        }, $html) ?? $html;

        return preg_replace('/<(p|figure|a|div)\b[^>]*>\s*(?:&nbsp;|&#160;|\xC2\xA0|\s)*<\/\1>/i', '', $html) ?? $html;
    }
}
