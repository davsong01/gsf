<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_record_book_uploads', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('sha256')->index();
            $table->string('record_type', 16)->default('member')->after('status')->index();
            $table->unsignedInteger('extracted_records_count')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('extracted_records_count');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('source', 32)->default('registration')->after('chapter_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('source');
        });

        Schema::table('campus_record_book_uploads', function (Blueprint $table) {
            $table->dropColumn(['status', 'record_type', 'extracted_records_count', 'completed_at']);
        });
    }
};
