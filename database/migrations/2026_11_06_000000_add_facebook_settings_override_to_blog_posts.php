<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_posts') && ! Schema::hasColumn('blog_posts', 'facebook_settings_override')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->boolean('facebook_settings_override')->default(false)->after('facebook_publish_requested');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blog_posts') && Schema::hasColumn('blog_posts', 'facebook_settings_override')) {
            Schema::table('blog_posts', fn (Blueprint $table) => $table->dropColumn('facebook_settings_override'));
        }
    }
};
