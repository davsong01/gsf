<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->string('registration_user_type')
              ->default('participant')
              ->after('level');
    });

    // Schema::table('conference_plans', function (Blueprint $table) {
    //     $table->string('registration_user_type')
    //           ->default('participant')
    //           ->after('level');
    // });

    DB::table('transactions')
    ->whereNull('registration_user_type')
    ->where('level', 'Moderator')
    ->update(['registration_user_type' => 'moderator']);

    DB::table('transactions')
    ->whereNull('registration_user_type')
    ->where('level', 'Participant')
    ->update(['registration_user_type' => 'participant']);

    DB::table('conference_plans')
        ->whereNull('registration_user_type')
        ->update(['registration_user_type' => 'participant']);

    DB::table('transactions')
    ->whereNull('registration_user_type')
    ->whereIn('level', ['Official'])
    ->update(['registration_user_type' => 'official']);
}

public function down(): void
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->dropColumn('registration_user_type');
    });

    Schema::table('conference_plans', function (Blueprint $table) {
        $table->dropColumn('registration_user_type');
    });
}
};
