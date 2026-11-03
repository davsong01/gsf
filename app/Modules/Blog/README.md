# GSF Blog Module

This module owns the new content workflow independently from the legacy `App\Models\Post` reward-post flow.

## Install

1. Copy `app/Modules/Blog` and `resources/views/modules/blog` into the target Laravel application.
2. Copy `database/migrations/2026_11_03_180000_create_blog_module_tables.php`.
3. Include `require base_path('app/Modules/Blog/routes.php');` from the web route loader.
4. Run `php artisan migrate`.
5. Run `php artisan db:seed --class=BlogPermissionSeeder`.
6. Run `php artisan storage:link` for uploaded images.

The host application must provide the existing `users`, `stakeholders`, `chapters`, `fields`, and `zones` tables, plus the `auth` and `stakeholder` guards.

## Permission contract

The module uses these capability slugs:

- `blog.view`
- `blog.create`
- `blog.manage-own`
- `blog.edit`
- `blog.review`
- `blog.publish`
- `blog.archive`
- `blog.categories`
- `blog.comments`

Admin web users can use the workspace through the existing admin role. Stakeholders receive scoped ownership based on their chapter, field, or zone assignment; categories remain admin/editorial capabilities.

## Routes

- Public newsroom: `/blog`
- Article: `/blog/{slug}`
- RSS: `/blog/rss.xml`
- Sitemap: `/blog/sitemap.xml`
- Scoped newsroom: `/blog/network/{scopeType}/{scopeId}`
- Admin workspace: `/admin/blog/posts`
- Stakeholder workspace: `/stakeholders/blog/posts`
