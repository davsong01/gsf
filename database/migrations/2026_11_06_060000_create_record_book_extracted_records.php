<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('campus_record_book_uploads')) {
            Schema::table('campus_record_book_uploads', function (Blueprint $table) {
                if (!Schema::hasColumn('campus_record_book_uploads', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable();
                }
                if (!Schema::hasColumn('campus_record_book_uploads', 'extracted_at')) {
                    $table->timestamp('extracted_at')->nullable();
                }
                if (!Schema::hasColumn('campus_record_book_uploads', 'extraction_response')) {
                    $table->longText('extraction_response')->nullable();
                }
            });
        }

        if (!Schema::hasTable('campus_record_book_extracted_records')) {
            Schema::create('campus_record_book_extracted_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('upload_id')->constrained('campus_record_book_uploads')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('data');
                $table->string('status', 20)->default('pending')->index();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->string('approved_by_guard', 20)->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                $table->index(['upload_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_record_book_extracted_records');

        if (Schema::hasTable('campus_record_book_uploads') && Schema::hasColumn('campus_record_book_uploads', 'approved_at')) {
            Schema::table('campus_record_book_uploads', function (Blueprint $table) {
                $table->dropColumn(array_filter(['approved_at', 'extracted_at', 'extraction_response'], fn ($column) => Schema::hasColumn('campus_record_book_uploads', $column)));
            });
        }
    }
};
