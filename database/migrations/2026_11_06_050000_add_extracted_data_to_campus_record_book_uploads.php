<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('campus_record_book_uploads')) {
            return;
        }

        Schema::table('campus_record_book_uploads', function (Blueprint $table) {
            if (!Schema::hasColumn('campus_record_book_uploads', 'extracted_data')) {
                $table->longText('extracted_data')->nullable();
            }
            if (!Schema::hasColumn('campus_record_book_uploads', 'extraction_error')) {
                $table->text('extraction_error')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('campus_record_book_uploads')) {
            return;
        }

        Schema::table('campus_record_book_uploads', function (Blueprint $table) {
            $columns = array_filter(['extracted_data', 'extraction_error'], fn ($column) => Schema::hasColumn('campus_record_book_uploads', $column));
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
