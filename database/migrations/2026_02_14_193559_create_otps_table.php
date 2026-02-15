<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('otps')) {
            Schema::create('otps', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('userable_id');
                $table->string('userable_type', 100);
                $table->index(['userable_id', 'userable_type']);

                $table->string('otp', 30);
                $table->string('type', 50);

                $table->timestamp('expires_at')->index();

                $table->timestamps();
                $table->index(['userable_id', 'userable_type', 'type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
