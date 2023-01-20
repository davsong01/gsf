<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
           
            $table->string('purpose')->default('conference');
            $table->integer('hostel_id')->nullable();
            $table->string('food_id')->nullable();
            $table->integer('slot')->default(0);
            $table->integer('slot_filled')->default(0);
            $table->string('type');//individual:1, fellowship:2,alumni:3,Nec:4,Donations:5,
            $table->string('level');//Admin 
            $table->string('official')->nullable();
            
            $table->string('amount_paid')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transid')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->enum('registration_status', ['Pending', 'Complete'])->default('Pending');//Pending or Complete
            $table->softDeletes();
            
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
        Schema::dropIfExists('payments');
    }
}
