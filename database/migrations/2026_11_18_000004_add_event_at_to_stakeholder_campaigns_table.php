<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stakeholder_campaigns') && ! Schema::hasColumn('stakeholder_campaigns', 'event_at')) {
            Schema::table('stakeholder_campaigns', function (Blueprint $table) {
                $table->dateTime('event_at')->nullable()->after('open_until');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stakeholder_campaigns') && Schema::hasColumn('stakeholder_campaigns', 'event_at')) {
            Schema::table('stakeholder_campaigns', function (Blueprint $table) {
                $table->dropColumn('event_at');
            });
        }
    }
};
