<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_id')->nullable()->unique();
            $table->string('legacy_author_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropUnique(['wordpress_id']);
            $table->dropColumn(['wordpress_id', 'legacy_author_name']);
        });
    }
};
