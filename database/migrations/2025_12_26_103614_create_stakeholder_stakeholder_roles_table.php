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
        if (!Schema::hasTable('stakeholder_stakeholder_roles')) {
            Schema::create('stakeholder_stakeholder_roles', function (Blueprint $table) {
                $table->foreignId('stakeholder_id')->constrained('stakeholders')->cascadeOnDelete();
                $table->foreignId('stakeholder_role_id')->constrained('stakeholder_roles')->cascadeOnDelete();
                $table->primary(['stakeholder_id', 'stakeholder_role_id'], 'stakeholder_roles_pk');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stakeholder_stakeholder_roles');
    }
};
