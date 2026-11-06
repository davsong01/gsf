<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('campus_record_book_uploads') && !Schema::hasColumn('campus_record_book_uploads', 'extraction_response')) {
            Schema::table('campus_record_book_uploads', function (Blueprint $table) {
                $table->longText('extraction_response')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('campus_record_book_uploads') && Schema::hasColumn('campus_record_book_uploads', 'extraction_response')) {
            Schema::table('campus_record_book_uploads', function (Blueprint $table) {
                $table->dropColumn('extraction_response');
            });
        }
    }
};
