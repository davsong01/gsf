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
        if (!Schema::hasTable('ministry_fields')) {
            Schema::create('ministry_fields', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ministry_id');
                $table->enum('field_usage',['registration','allocation','both'])->default('both');

                $table->string('name'); // field key
                $table->string('label');
                $table->integer('display_order')->nullable();
                $table->enum('type', ['text', 'number', 'email', 'select', 'textarea', 'checkbox', 'radio']);
                $table->json('options')->nullable(); // for select/radio/checkbox
                $table->json('registration_types'); // e.g., [1,2]
                $table->boolean('required')->default(false);
                $table->boolean('status')->default(true);
                $table->boolean('has_other_option')->default(false);
                $table->string('onchange')->nullable();
                $table->json('depends_on')->nullable(); // conditional logic
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ministry_fields')) {
            Schema::dropIfExists('ministry_fields');
        }
    }
};
