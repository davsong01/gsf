<?php

namespace App\Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

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

    public static function campusPostsRequireReview(): bool
    {
        return filter_var(static::value('campus_posts_require_review', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function campusPostsCanFeature(): bool
    {
        return filter_var(static::value('campus_posts_can_feature', '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function campusPostsCanAllowComments(): bool
    {
        return filter_var(static::value('campus_posts_can_allow_comments', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function campusEventsCanPublishToFacebook(): bool
    {
        return static::boolean('campus_events_facebook_enabled', false);
    }

    public static function campusEventsFacebookRequireReview(): bool
    {
        return static::boolean('campus_events_facebook_review_required', true);
    }

    public static function campusPostsCanPublishToFacebook(): bool
    {
        return static::boolean('campus_posts_facebook_enabled', false);
    }

    public static function campusPostsFacebookRequireReview(): bool
    {
        return static::boolean('campus_posts_facebook_review_required', true);
    }

    public static function facebookPageId(): ?string
    {
        return static::value('facebook_page_id');
    }

    public static function facebookPageAccessToken(): ?string
    {
        $encrypted = static::value('facebook_page_access_token');
        if (! filled($encrypted)) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (Throwable) {
            // A legacy/plain value is not expected, but accepting it keeps a
            // manually configured token usable during a staged rollout.
            return $encrypted;
        }
    }

    private static function boolean(string $key, bool $default): bool
    {
        return filter_var(static::value($key, $default ? '1' : '0'), FILTER_VALIDATE_BOOLEAN);
    }
}
