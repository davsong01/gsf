<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('gsf_constitution_path')->nullable();
            $table->string('gsf_code_of_conduct_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'gsf_constitution_path',
                'gsf_code_of_conduct_path',
            ]);
        });
    }
};
