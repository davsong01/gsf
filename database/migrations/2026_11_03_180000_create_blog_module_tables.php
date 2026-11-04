<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // These tables may already exist on installations that received the
        // blog schema before this migration was recorded. Keep existing data
        // and let the migration ledger catch up instead of failing deployment.
        if (!Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('blog_tags')) {
            Schema::create('blog_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->id();
                $table->string('type')->default('article');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt')->nullable();
                $table->longText('body')->nullable();
                $table->string('featured_image')->nullable();
                $table->string('video_url')->nullable();
                $table->string('author_type')->nullable();
                $table->unsignedBigInteger('author_id')->nullable();
                $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
                $table->string('status')->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->string('seo_title')->nullable();
                $table->text('seo_description')->nullable();
                $table->string('social_image')->nullable();
                $table->string('canonical_url')->nullable();
                $table->string('scope_type')->default('national')->index();
                $table->unsignedBigInteger('scope_id')->nullable()->index();
                $table->unsignedBigInteger('view_count')->default(0);
                $table->boolean('allow_comments')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->index(['author_type', 'author_id']);
                $table->index(['scope_type', 'scope_id']);
            });
        }

        if (!Schema::hasTable('blog_post_tag')) {
            Schema::create('blog_post_tag', function (Blueprint $table) {
                $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained('blog_tags')->cascadeOnDelete();
                $table->primary(['post_id', 'tag_id']);
            });
        }

        if (!Schema::hasTable('blog_post_related')) {
            Schema::create('blog_post_related', function (Blueprint $table) {
                $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
                $table->foreignId('related_post_id')->constrained('blog_posts')->cascadeOnDelete();
                $table->primary(['post_id', 'related_post_id']);
            });
        }

        if (!Schema::hasTable('blog_comments')) {
            Schema::create('blog_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->text('body');
                $table->string('status')->default('pending')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('blog_redirects')) {
            Schema::create('blog_redirects', function (Blueprint $table) {
                $table->id();
                $table->string('from_path')->unique();
                $table->string('to_path');
                $table->unsignedSmallInteger('status_code')->default(301);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_redirects');
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('blog_post_related');
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};
