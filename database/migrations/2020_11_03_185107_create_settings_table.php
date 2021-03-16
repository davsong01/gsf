<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('registration_fee')->default(0);
            $table->string('official_email');
            $table->integer('alumni_fee');
            $table->boolean('new_alumni');
            $table->date('close_registration');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('conference_theme');
            $table->text('conference_overview');

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
        Schema::dropIfExists('settings');
    }
}
