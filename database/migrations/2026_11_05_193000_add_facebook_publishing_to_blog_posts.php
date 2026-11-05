<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blog_posts')) {
            return;
        }

        Schema::table('blog_posts', function (Blueprint $table) {
            if (! Schema::hasColumn('blog_posts', 'facebook_publish_requested')) {
                $table->boolean('facebook_publish_requested')->default(false);
            }
            if (! Schema::hasColumn('blog_posts', 'facebook_status')) {
                $table->string('facebook_status', 32)->default('not_requested')->index();
            }
            if (! Schema::hasColumn('blog_posts', 'facebook_post_id')) {
                $table->string('facebook_post_id')->nullable();
            }
            if (! Schema::hasColumn('blog_posts', 'facebook_published_at')) {
                $table->timestamp('facebook_published_at')->nullable();
            }
            if (! Schema::hasColumn('blog_posts', 'facebook_error')) {
                $table->text('facebook_error')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('blog_posts')) {
            return;
        }

        $columns = array_values(array_filter([
            'facebook_publish_requested',
            'facebook_status',
            'facebook_post_id',
            'facebook_published_at',
            'facebook_error',
        ], fn (string $column) => Schema::hasColumn('blog_posts', $column)));

        if ($columns !== []) {
            Schema::table('blog_posts', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};
