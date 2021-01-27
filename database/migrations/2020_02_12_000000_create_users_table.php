<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('conference_number')->nullable();
            $table->integer('hostel_id')->nullable();
            $table->string('food_id')->nullable();
            $table->integer('slot')->default(0);
            $table->integer('slot_filled')->default(0);
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('sex')->nullable();
            $table->integer('chapter')->nullable();
            $table->string('type');
            $table->string('level');
            $table->string('passport')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transid')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->string('registration_status')->default('Pending');//Pending or Complete
            $table->string('password');

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
