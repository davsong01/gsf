<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStakeholderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stakeholder_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('report_id')->nullable();
            $table->integer('chapter_id');
            $table->integer('zone_id');
            $table->integer('field_id');
            $table->text('description')->nullable();
            $table->double('amount');
            $table->string('image');
            $table->string('month');
            $table->string('year');
            $table->string('insession');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stakeholder_payments');
    }
}
