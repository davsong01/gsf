<?php

use Illuminate\Support\Str;
use App\Models\ConferencePlan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conference_plans', function (Blueprint $table) {
            $table->string('slug', 191)->after('level')->unique()->nullable();
        });

        ConferencePlan::whereNull('slug')->get()->each(function ($plan) {
                $plan->slug = Str::slug($plan->title);
                $plan->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_plans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
