<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->longText('gsf_family_song_lyrics')->nullable();
            $table->string('gsf_family_song_audio_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'gsf_family_song_lyrics',
                'gsf_family_song_audio_path',
            ]);
        });
    }
};
