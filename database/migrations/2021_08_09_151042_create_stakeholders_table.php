<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStakeholdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stakeholders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->nullable();
            $table->string('community_id')->nullable();
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->string('password');
            $table->string('zone_id')->nullable();//Office Name
            $table->string('field_id')->nullable();//Office Name
            $table->string('chapter_id')->nullable();
            $table->string('signature')->nullable();
            $table->string('gen_sec_signature')->nullable();
            $table->string('fin_sec_signature')->nullable();
            $table->string('evang_sec_signature')->nullable();
            $table->string('phone')->nullable();
            $table->string('day')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('role');//1:President, 2:Zonal Pastor, 3:Field Pastor, 4:Secretariat
            $table->string('portfolio')->nullable();//Stakeholder portfolio, majorly for NEC members for sending emails
            $table->rememberToken();
 
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
        Schema::dropIfExists('stakeholders');
    }
}
