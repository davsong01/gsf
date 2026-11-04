<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->index();
        });

        if (Schema::hasTable('blog_redirects')) {
            DB::table('blog_redirects')->where('to_path', 'like', '/blog/%')->orderBy('id')
                ->chunkById(100, function ($redirects) {
                    foreach ($redirects as $redirect) {
                        DB::table('blog_redirects')->where('id', $redirect->id)->update([
                            'to_path' => '/news/'.substr($redirect->to_path, strlen('/blog/')),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blog_redirects')) {
            DB::table('blog_redirects')->where('to_path', 'like', '/news/%')->orderBy('id')
                ->chunkById(100, function ($redirects) {
                    foreach ($redirects as $redirect) {
                        DB::table('blog_redirects')->where('id', $redirect->id)->update([
                            'to_path' => '/blog/'.substr($redirect->to_path, strlen('/news/')),
                        ]);
                    }
                });
        }

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropColumn('is_featured');
        });
    }
};
