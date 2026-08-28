<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stakeholders', function (Blueprint $table) {
            if (! Schema::hasColumn('stakeholders', 'access_appraisal_system')) {
                $table->boolean('access_appraisal_system')->default(false)->after('credentials_sent');
            }

            if (! Schema::hasColumn('stakeholders', 'access_appraisal_evaluation')) {
                $table->boolean('access_appraisal_evaluation')->default(false)->after('access_appraisal_system');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stakeholders', function (Blueprint $table) {
            if (Schema::hasColumn('stakeholders', 'access_appraisal_evaluation')) {
                $table->dropColumn('access_appraisal_evaluation');
            }

            if (Schema::hasColumn('stakeholders', 'access_appraisal_system')) {
                $table->dropColumn('access_appraisal_system');
            }
        });
    }
};
