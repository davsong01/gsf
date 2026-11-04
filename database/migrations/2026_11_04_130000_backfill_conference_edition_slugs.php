<?php

use App\Models\ConferenceEdition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        ConferenceEdition::query()->orderBy('id')->each(function (ConferenceEdition $edition): void {
            $base = Str::slug($edition->slug ?: $edition->conference_theme);
            $base = $base !== '' ? $base : 'conference-'.$edition->id;
            $slug = $base;
            $suffix = 2;

            while (ConferenceEdition::query()
                ->where('slug', $slug)
                ->whereKeyNot($edition->id)
                ->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            if ($edition->slug !== $slug) {
                $edition->updateQuietly(['slug' => $slug]);
            }
        });
    }

    public function down(): void
    {
        // Slug normalization is intentionally not reversed.
    }
};
