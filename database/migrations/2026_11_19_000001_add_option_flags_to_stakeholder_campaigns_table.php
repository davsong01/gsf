<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stakeholder_campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('stakeholder_campaigns', 'enable_field_options')) {
                $table->boolean('enable_field_options')->default(false);
            }
            if (!Schema::hasColumn('stakeholder_campaigns', 'enable_zone_options')) {
                $table->boolean('enable_zone_options')->default(false);
            }
            if (!Schema::hasColumn('stakeholder_campaigns', 'enable_chapter_option')) {
                $table->boolean('enable_chapter_option')->default(false);
            }
            if (!Schema::hasColumn('stakeholder_campaigns', 'enable_designation_options')) {
                $table->boolean('enable_designation_options')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('stakeholder_campaigns', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('stakeholder_campaigns', 'enable_field_options') ? 'enable_field_options' : null,
                Schema::hasColumn('stakeholder_campaigns', 'enable_zone_options') ? 'enable_zone_options' : null,
                Schema::hasColumn('stakeholder_campaigns', 'enable_chapter_option') ? 'enable_chapter_option' : null,
                Schema::hasColumn('stakeholder_campaigns', 'enable_designation_options') ? 'enable_designation_options' : null,
            ]);
            if ($columns) $table->dropColumn($columns);
        });
    }
};
