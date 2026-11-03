<?php

namespace App\Modules\Blog;

use App\Models\Stakeholder;
use App\Models\User;

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
    public const COMMENTS = 'blog.comments';

    public static function all(): array
    {
        return [self::VIEW, self::CREATE, self::EDIT, self::MANAGE_OWN, self::REVIEW, self::PUBLISH, self::ARCHIVE, self::CATEGORIES, self::COMMENTS];
    }

    public static function allows($user, string $permission): bool
    {
        if (!$user) return false;
        if ($user instanceof User) return $user->isAdmin() || in_array($permission, $user->permissions ?? [], true);
        if ($user instanceof Stakeholder) return $user->hasPermission($permission);
        return false;
    }
}
