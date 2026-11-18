<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stakeholder_campaigns') && ! Schema::hasColumn('stakeholder_campaigns', 'status')) {
            Schema::table('stakeholder_campaigns', function (Blueprint $table) {
                $table->string('status')->default('draft')->after('open_until');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stakeholder_campaigns') && Schema::hasColumn('stakeholder_campaigns', 'status')) {
            Schema::table('stakeholder_campaigns', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
