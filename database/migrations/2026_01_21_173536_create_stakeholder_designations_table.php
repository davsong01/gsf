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
        // Create stakeholder_designations table if it doesn't exist
        if (!Schema::hasTable('stakeholder_designations')) {
            Schema::create('stakeholder_designations', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->integer('field_id')->nullable();
                $table->integer('zone_id')->nullable();
                $table->integer('chapter_id')->nullable();
                $table->integer('order')->default(0)->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->enum('type', ['nec', 'chapter_executive'])->default('nec');
                $table->timestamps();
            });
        }

        // Modify stakeholders table if columns do not exist
        if (Schema::hasTable('stakeholders')) {
            if (!Schema::hasColumn('stakeholders', 'tenure')) {
                Schema::table('stakeholders', function (Blueprint $table) {
                    $table->string('tenure')->nullable()->after('role_id');
                });
            }
            if (!Schema::hasColumn('stakeholders', 'gender')) {
                Schema::table('stakeholders', function (Blueprint $table) {
                    $table->enum('gender', ['Male', 'Female'])->nullable()->after('tenure');
                });
            }
            if (!Schema::hasColumn('stakeholders', 'designation_id')) {
                Schema::table('stakeholders', function (Blueprint $table) {
                    $table->integer('designation_id')->nullable()->after('tenure');
                });
            }
            // Rename portfolio to office if portfolio exists and office does not
            if (Schema::hasColumn('stakeholders', 'portfolio') && !Schema::hasColumn('stakeholders', 'office')) {
                Schema::table('stakeholders', function (Blueprint $table) {
                    $table->renameColumn('portfolio', 'office');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop stakeholder_designations table if it exists
        if (Schema::hasTable('stakeholder_designations')) {
            Schema::dropIfExists('stakeholder_designations');
        }

        // Revert stakeholders table changes if columns exist
        if (Schema::hasTable('stakeholders')) {
            $columnsToDrop = [];
            if (Schema::hasColumn('stakeholders', 'tenure')) {
                $columnsToDrop[] = 'tenure';
            }
            if (Schema::hasColumn('stakeholders', 'gender')) {
                $columnsToDrop[] = 'gender';
            }
            if (Schema::hasColumn('stakeholders', 'designation_id')) {
                $columnsToDrop[] = 'designation_id';
            }
            if (!empty($columnsToDrop)) {
                Schema::table('stakeholders', function (Blueprint $table) use ($columnsToDrop) {
                    $table->dropColumn($columnsToDrop);
                });
            }
            // Rename office back to portfolio if office exists and portfolio does not
            if (Schema::hasColumn('stakeholders', 'office') && !Schema::hasColumn('stakeholders', 'portfolio')) {
                Schema::table('stakeholders', function (Blueprint $table) {
                    $table->renameColumn('office', 'portfolio');
                });
            }
        }
    }
};
