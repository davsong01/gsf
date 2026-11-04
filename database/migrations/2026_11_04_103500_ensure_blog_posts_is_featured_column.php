<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_posts') && !Schema::hasColumn('blog_posts', 'is_featured')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->boolean('is_featured')->default(false)->index();
            });
        }
    }

    public function down(): void
    {
        // This repair migration leaves the column lifecycle to the original feature migration.
    }
};
