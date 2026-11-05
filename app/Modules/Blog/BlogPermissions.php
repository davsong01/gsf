<?php

namespace App\Modules\Blog;

use App\Models\Stakeholder;
use App\Models\User;
use App\Modules\Blog\Models\BlogPost;

final class BlogPermissions
{
    public const VIEW = 'blog.view';
    public const CREATE = 'blog.create';
    public const EDIT = 'blog.edit';
    public const MANAGE_OWN = 'blog.manage-own';
    public const REVIEW = 'blog.review';
    public const PUBLISH = 'blog.publish';
    public const ARCHIVE = 'blog.archive';
    public const CATEGORIES = 'blog.categories';
    public const TAGS = 'blog.tags';
    public const COMMENTS = 'blog.comments';
    public const CLEAR_COMMENTS = 'blog.comments.clear-all';
    public const FEATURE = 'make_post_featured';
    public const SETTINGS = 'blog.settings';

    public static function all(): array
    {
        return [self::VIEW, self::CREATE, self::EDIT, self::MANAGE_OWN, self::REVIEW, self::PUBLISH, self::ARCHIVE, self::CATEGORIES, self::TAGS, self::COMMENTS, self::CLEAR_COMMENTS, self::FEATURE, self::SETTINGS];
    }

    public static function allows($user, string $permission): bool
    {
        if (!$user) return false;
        if ($user instanceof User) return (int) $user->role === 1 || in_array($permission, $user->permissions ?? [], true);
        if ($user instanceof Stakeholder) return $user->hasPermission($permission);
        return false;
    }

    public static function canEditPost($user, BlogPost $post): bool
    {
        if (!$user) {
            return false;
        }

        $ownsPost = $post->author_type === $user::class && (int) $post->author_id === (int) $user->id;

        return self::allows($user, $ownsPost ? self::MANAGE_OWN : self::EDIT);
    }

    public static function canFeaturePosts($user): bool
    {
        return $user instanceof User && ((int) $user->role === 1 || in_array(self::FEATURE, $user->permissions ?? [], true));
    }

    public static function isWorkspaceUser($user): bool
    {
        if ($user instanceof User) {
            return (int) $user->role === 1 || (bool) array_intersect(self::all(), $user->permissions ?? []);
        }

        if ($user instanceof Stakeholder) {
            foreach (self::all() as $permission) {
                if ($user->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
    }
}
