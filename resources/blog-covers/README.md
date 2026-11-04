# Blog cover backfill

The PNG files in this directory are original GSF editorial infographics. `manifest.json` maps each post slug to its cover. The source artwork was drawn with `scripts/generate-blog-covers.mjs`; no third-party photographs or licenses are involved.

After deploying this directory and `database/seeders/BackfillBlogFeaturedCoversSeeder.php`, run:

```sh
php artisan db:seed --class=BackfillBlogFeaturedCoversSeeder --force
```

The seeder copies each missing cover through `BlogMedia::upload`, which uses `FileUploadService::publicUpload`. Existing images that resolve locally are preserved. Re-running the seeder is safe.
