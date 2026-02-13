<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('stakeholders', 'deleted_at')) {
            Schema::table('stakeholders', function (Blueprint $table) {
                $table->softDeletes(); // adds deleted_at
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('stakeholders', 'deleted_at')) {
            Schema::table('stakeholders', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }

};
