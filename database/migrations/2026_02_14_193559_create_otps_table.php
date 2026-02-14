<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();

            $table->morphs('userable');
            // creates: userable_id & userable_type

            $table->string('otp', 30);
            $table->string('type'); // signup_verification, forgot_password, login_otp

            $table->timestamp('expires_at')->index();

            $table->timestamps();
            $table->index(['userable_id', 'userable_type', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
