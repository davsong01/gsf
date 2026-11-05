<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('email_contacts') && !Schema::hasColumn('email_contacts', 'chapter_id')) {
            Schema::table('email_contacts', function (Blueprint $table) {
                $table->foreignId('chapter_id')->nullable()->after('user_id')->constrained('chapters')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('email_contacts', 'chapter_id')) {
            Schema::table('email_contacts', function (Blueprint $table) {
                $table->dropConstrainedForeignId('chapter_id');
            });
        }
    }
};
