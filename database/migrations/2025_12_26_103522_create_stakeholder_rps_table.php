<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stakeholder_rps', function (Blueprint $table) {
            $table->foreignId('stakeholder_role_id')->nullable();
            $table->foreignId('stakeholder_permission_id')->nullable();
            $table->primary(['stakeholder_role_id', 'stakeholder_permission_id'], 'stakeholder_rps_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stakeholder_rps');
    }
};
