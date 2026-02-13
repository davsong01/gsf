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
        if (!Schema::hasTable('necs')) {
            Schema::create('necs', function (Blueprint $table) {
                $table->id();
                if (!Schema::hasColumn('necs', 'name')) $table->string('name');
                if (!Schema::hasColumn('necs', 'email')) $table->string('email')->nullable();
                if (!Schema::hasColumn('necs', 'phone')) $table->string('phone')->nullable();
                if (!Schema::hasColumn('necs', 'office')) $table->string('office')->nullable();
                if (!Schema::hasColumn('necs', 'tenure')) $table->string('tenure')->nullable();
                if (!Schema::hasColumn('necs', 'bday')) $table->string('bday')->nullable();
                if (!Schema::hasColumn('necs', 'passport')) $table->string('passport')->nullable();
                if (!Schema::hasColumn('necs', 'order')) $table->integer('order')->nullable();
                if (!Schema::hasColumn('necs', 'gender')) $table->string('gender')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('necs')) {
            $columnsToDrop = [];
            if (Schema::hasColumn('necs', 'name')) $columnsToDrop[] = 'name';
            if (Schema::hasColumn('necs', 'email')) $columnsToDrop[] = 'email';
            if (Schema::hasColumn('necs', 'phone')) $columnsToDrop[] = 'phone';
            if (Schema::hasColumn('necs', 'office')) $columnsToDrop[] = 'office';
            if (Schema::hasColumn('necs', 'tenure')) $columnsToDrop[] = 'tenure';
            if (Schema::hasColumn('necs', 'bday')) $columnsToDrop[] = 'bday';
            if (Schema::hasColumn('necs', 'passport')) $columnsToDrop[] = 'passport';
            if (Schema::hasColumn('necs', 'order')) $columnsToDrop[] = 'order';
            if (Schema::hasColumn('necs', 'gender')) $columnsToDrop[] = 'gender';
            if (!empty($columnsToDrop)) {
                Schema::table('necs', function (Blueprint $table) use ($columnsToDrop) {
                    $table->dropColumn($columnsToDrop);
                });
            }
            Schema::dropIfExists('necs');
        }
    }
};
