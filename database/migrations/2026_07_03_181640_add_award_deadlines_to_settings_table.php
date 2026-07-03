<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('award_settings', function (Blueprint $table) {

            $table->dropColumn([
                'allow_chapter_edit',
                'allow_chapter_comment',
                'allow_chapter_approval',

                'allow_zone_edit',
                'allow_zone_comment',
                'allow_zone_approval',

                'allow_field_edit',
                'allow_field_comment',
                'allow_field_approval',
            ]);
        });

        Schema::table('award_settings', function (Blueprint $table) {

            $table->dateTime('allow_chapter_edit')->nullable();
            $table->dateTime('allow_chapter_comment')->nullable();
            $table->dateTime('allow_chapter_approval')->nullable();

            $table->dateTime('allow_zone_edit')->nullable();
            $table->dateTime('allow_zone_comment')->nullable();
            $table->dateTime('allow_zone_approval')->nullable();

            $table->dateTime('allow_field_edit')->nullable();
            $table->dateTime('allow_field_comment')->nullable();
            $table->dateTime('allow_field_approval')->nullable();

            $table->dateTime('first_class_awards_deadline')->nullable();
            $table->dateTime('etf_awards_deadline')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('award_settings', function (Blueprint $table) {

            $table->dropColumn([
                'allow_chapter_edit',
                'allow_chapter_comment',
                'allow_chapter_approval',

                'allow_zone_edit',
                'allow_zone_comment',
                'allow_zone_approval',

                'allow_field_edit',
                'allow_field_comment',
                'allow_field_approval',

                'first_class_awards_deadline',
                'etf_awards_deadline',
            ]);
        });

        Schema::table('award_settings', function (Blueprint $table) {

            $table->string('allow_chapter_edit')->nullable();
            $table->string('allow_chapter_comment')->nullable();
            $table->string('allow_chapter_approval')->nullable();

            $table->string('allow_zone_edit')->nullable();
            $table->string('allow_zone_comment')->nullable();
            $table->string('allow_zone_approval')->nullable();

            $table->string('allow_field_edit')->nullable();
            $table->string('allow_field_comment')->nullable();
            $table->string('allow_field_approval')->nullable();
        });
    }
};
