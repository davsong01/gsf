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
            $table->string('family_id')->nullable();
            $table->string('slug');
            $table->string('name');
            $table->integer('chapter_id')->nullable();
            $table->string('email')->unique();
            $table->integer('show_email')->default(0);
            $table->string('phone')->nullable();
            $table->integer('show_phone')->default(0);
            $table->string('sex')->nullable();
            $table->string('passport')->nullable();
            $table->integer('role')->default(2);
            $table->integer('status')->default(0); //0: Member, 1: Alumni
            $table->string('portfolio_session')->nullable(); 
            $table->string('skills')->nullable(); 
            $table->string('matric_year')->nullable();
            $table->string('graduation_year')->nullable();
            $table->integer('open_to_work')->nullable(); //1 : open to work;
            $table->date('dob')->nullable();
            $table->string('program')->nullable();
            $table->string('course')->nullable();
            $table->integer('course_duration')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->integer('hostel_id')->nullable();
            $table->string('food_id')->nullable();
            $table->integer('slot')->default(0);
            $table->integer('slot_filled')->default(0);
            $table->string('type')->nullable();//individual:1, fellowship:2,alumni:3,Nec:4,Donations:5,
            $table->string('level')->nullable();//Admin 
            $table->string('official')->nullable();
            
            $table->string('amount_paid')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transid')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->enum('registration_status', ['Pending', 'Complete'])->default('Pending');//Pending or Complete
           
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
