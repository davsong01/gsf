<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('blog_comments', 'chapter_id')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->foreignId('chapter_id')->nullable()->after('post_id')->constrained('chapters')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('blog_comments', 'chapter_id')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('chapter_id');
            });
        }
    }
};
