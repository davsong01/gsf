<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'enable_campus_record_book_upload')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->boolean('enable_campus_record_book_upload')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'enable_campus_record_book_upload')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('enable_campus_record_book_upload');
            });
        }
    }
};
