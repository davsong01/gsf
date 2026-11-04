<?php

namespace App\Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSetting extends Model
{
    protected $table = 'blog_settings';
    protected $guarded = [];

    public static function value(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    public static function commentsRequireApproval(): bool
    {
        return filter_var(static::value('comments_require_approval', '1'), FILTER_VALIDATE_BOOLEAN);
    }
}
