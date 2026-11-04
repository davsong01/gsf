<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('award_entries')) {
            return;
        }

        $columns = [
            'first_name' => fn (Blueprint $table) => $table->text('first_name')->nullable(),
            'select_institution' => fn (Blueprint $table) => $table->text('select_institution')->nullable(),
            'course_of_study' => fn (Blueprint $table) => $table->text('course_of_study')->nullable(),
            'cgpa' => fn (Blueprint $table) => $table->string('cgpa')->nullable(),
            'graduating_grade' => fn (Blueprint $table) => $table->string('graduating_grade')->nullable(),
            'legacy_category' => fn (Blueprint $table) => $table->string('legacy_category')->nullable(),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('award_entries', $column)) {
                Schema::table('award_entries', $definition);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('award_entries')) {
            return;
        }

        $columns = array_values(array_filter(
            ['first_name', 'select_institution', 'course_of_study', 'cgpa', 'graduating_grade', 'legacy_category'],
            fn (string $column) => Schema::hasColumn('award_entries', $column)
        ));

        if ($columns) {
            Schema::table('award_entries', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};
