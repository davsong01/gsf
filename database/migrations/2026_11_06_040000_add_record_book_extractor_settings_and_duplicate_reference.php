<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('campus_record_book_uploads') && !Schema::hasColumn('campus_record_book_uploads', 'duplicate_of_upload_id')) {
            Schema::table('campus_record_book_uploads', function (Blueprint $table) {
                $table->foreignId('duplicate_of_upload_id')->nullable()->after('record_type')
                    ->constrained('campus_record_book_uploads')->nullOnDelete();
            });
        }

        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'record_book_openai_api_key')) {
                    $table->longText('record_book_openai_api_key')->nullable();
                }
                if (!Schema::hasColumn('settings', 'record_book_openai_model')) {
                    $table->string('record_book_openai_model', 100)->default('gpt-4.1-mini');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                $columns = array_filter(['record_book_openai_api_key', 'record_book_openai_model'], fn ($column) => Schema::hasColumn('settings', $column));
                if ($columns) $table->dropColumn($columns);
            });
        }

        if (Schema::hasTable('campus_record_book_uploads') && Schema::hasColumn('campus_record_book_uploads', 'duplicate_of_upload_id')) {
            Schema::table('campus_record_book_uploads', function (Blueprint $table) {
                $table->dropConstrainedForeignId('duplicate_of_upload_id');
            });
        }
    }
};
