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
- `blog.comments.clear-all`
- `blog.settings`
- `make_post_featured`

Admin web users can use the workspace through the existing admin role. Stakeholders receive scoped ownership based on their chapter, field, or zone assignment; categories remain admin/editorial capabilities.

## Routes

- Public newsroom: `/news`
- Article: `/news/{slug}`
- RSS: `/news/rss.xml`
- Sitemap: `/news/sitemap.xml`
- Scoped newsroom: `/news/network/{scopeType}/{scopeId}`
- Admin workspace: `/admin/blog/posts`
- Stakeholder workspace: `/stakeholders/blog/posts`

## Scheduled publishing

Set a long random `BLOG_SCHEDULE_TOKEN` in the application environment, then call
`GET /cron/blog/process-scheduled-posts?token=YOUR_TOKEN` from an external cron
service once per minute. The endpoint publishes due scheduled blog posts and
events. Facebook requests publish at that time only when requested and eligible;
requests awaiting Facebook review stay queued for an admin. The endpoint processes
up to 100 due items per call, so it can be called again while `remaining_due` is
greater than zero.
