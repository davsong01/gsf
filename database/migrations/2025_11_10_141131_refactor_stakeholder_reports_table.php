<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stakeholder_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('stakeholder_reports', 'field_approved_at')) {
                $table->timestamp('field_approved_at')->nullable()->after('field_status');
            }

            if (!Schema::hasColumn('stakeholder_reports', 'field_rejected_at')) {
                $table->timestamp('field_rejected_at')->nullable()->after('field_approved_at');
            }

            if (!Schema::hasColumn('stakeholder_reports', 'zone_approved_at')) {
                $table->timestamp('zone_approved_at')->nullable()->after('zone_status');
            }

            if (!Schema::hasColumn('stakeholder_reports', 'zone_rejected_at')) {
                $table->timestamp('zone_rejected_at')->nullable()->after('zone_approved_at');
            }

            if (!Schema::hasColumn('stakeholder_reports', 'national_approved_at')) {
                $table->timestamp('national_approved_at')->nullable()->after('status_complete');
            }

            if (!Schema::hasColumn('stakeholder_reports', 'national_rejected_at')) {
                $table->timestamp('national_rejected_at')->nullable()->after('national_approved_at');
            }

            if (Schema::hasColumn('stakeholder_reports', 'status_complete')) {
                DB::statement('ALTER TABLE stakeholder_reports CHANGE status_complete national_status TINYINT(1) DEFAULT 0');
            }

            if (Schema::hasColumn('stakeholder_reports', 'zone_reject_comment')) {
                DB::statement('ALTER TABLE stakeholder_reports CHANGE zone_reject_comment zone_comment TEXT NULL');
            }

            if (Schema::hasColumn('stakeholder_reports', 'field_reject_comment')) {
                DB::statement('ALTER TABLE stakeholder_reports CHANGE field_reject_comment field_comment TEXT NULL');
            }

            if (Schema::hasColumn('stakeholder_reports', 'status_complete_reject_comment')) {
                DB::statement('ALTER TABLE stakeholder_reports CHANGE status_complete_reject_comment national_comment TEXT NULL');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stakeholder_reports', function (Blueprint $table) {
            // Drop only the timestamps we added
            $table->dropColumn([
                'field_approved_at',
                'field_rejected_at',
                'zone_approved_at',
                'zone_rejected_at',
                'national_approved_at',
                'national_rejected_at',
            ]);

            // Rename columns back to original
            $table->renameColumn('national_status', 'status_complete');
            $table->renameColumn('zone_comment', 'zone_reject_comment');
            $table->renameColumn('field_comment', 'field_reject_comment');
            $table->renameColumn('national_comment', 'status_complete_reject_comment');
        });
    }
};
