<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blog_settings')) {
            return;
        }

        $now = now();
        DB::table('blog_settings')->insertOrIgnore([
            ['key' => 'campus_posts_require_review', 'value' => '1', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'campus_posts_can_feature', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'campus_posts_can_allow_comments', 'value' => '1', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('blog_settings')) {
            return;
        }

        DB::table('blog_settings')->whereIn('key', [
            'campus_posts_require_review',
            'campus_posts_can_feature',
            'campus_posts_can_allow_comments',
        ])->delete();
    }
};
