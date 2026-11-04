<?php

namespace Database\Seeders;

use App\Models\StakeholderPermission;
use App\Models\StakeholderRole;
use Illuminate\Database\Seeder;

class BlogPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'blog.view' => 'View blog workspace',
            'blog.create' => 'Create blog posts',
            'blog.edit' => 'Edit any blog post',
            'blog.manage-own' => 'Manage own blog posts',
            'blog.review' => 'Review blog posts',
            'blog.publish' => 'Publish and schedule blog posts',
            'blog.archive' => 'Archive blog posts',
            'blog.categories' => 'Manage blog categories',
            'blog.tags' => 'Manage blog tags',
            'blog.comments' => 'Moderate blog comments',
            'blog.comments.clear-all' => 'Permanently clear all blog comments',
            'make_post_featured' => 'Choose featured posts for the public news page',
            'blog.settings' => 'Manage blog settings',
        ];

        foreach ($permissions as $slug => $name) {
            StakeholderPermission::firstOrCreate(['slug' => $slug], ['name' => $name, 'type' => 'action', 'description' => $name]);
        }

        $permissionIds = StakeholderPermission::whereIn('slug', array_keys($permissions))->pluck('id', 'slug');
        $rolePermissions = [
            'Chapter Representative' => ['blog.view', 'blog.create', 'blog.manage-own'],
            'Field Pastor' => ['blog.view', 'blog.create', 'blog.manage-own'],
            'Zonal Pastor' => ['blog.view', 'blog.create', 'blog.manage-own'],
            'Secretariat' => ['blog.view', 'blog.create', 'blog.manage-own', 'blog.review', 'blog.publish', 'blog.tags'],
            'NCP' => ['blog.view', 'blog.create', 'blog.manage-own', 'blog.review', 'blog.publish', 'blog.archive', 'blog.comments', 'blog.categories', 'blog.tags'],
            'Blog Writer' => ['blog.view', 'blog.create', 'blog.manage-own'],
            'Blog Editor' => ['blog.view', 'blog.create', 'blog.edit', 'blog.review', 'blog.comments', 'blog.tags'],
            'Blog Publisher' => ['blog.view', 'blog.create', 'blog.edit', 'blog.review', 'blog.publish', 'blog.archive', 'blog.comments', 'blog.categories', 'blog.tags'],
        ];

        foreach ($rolePermissions as $roleName => $slugs) {
            $role = StakeholderRole::firstOrCreate(['name' => $roleName], ['slug' => \Str::slug($roleName), 'status' => 'active']);
            $role->permissions()->syncWithoutDetaching(collect($slugs)->map(fn ($slug) => $permissionIds[$slug])->filter()->all());
        }
    }
}
