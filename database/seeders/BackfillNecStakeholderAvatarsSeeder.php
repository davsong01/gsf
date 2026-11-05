<?php

namespace Database\Seeders;

use App\Models\Stakeholder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BackfillNecStakeholderAvatarsSeeder extends Seeder
{
    private const IMAGE_DIRECTORY = 'site/images/leaders/nec';

    public function run(): void
    {
        if (! Schema::hasColumn('stakeholders', 'avatar')) {
            $this->command?->error('The stakeholders table has no avatar column. No changes were made.');

            return;
        }

        $directory = public_path(self::IMAGE_DIRECTORY);

        if (! is_dir($directory)) {
            $this->command?->error("Downloaded NEC images were not found at {$directory}. No changes were made.");

            return;
        }

        // Only browser-friendly raster images are eligible. HEIC files are left
        // untouched on disk and can be converted separately if needed.
        $imageFiles = glob($directory . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
        $imagesByName = [];

        foreach ($imageFiles as $imagePath) {
            if (! is_file($imagePath)) {
                continue;
            }

            $filename = pathinfo($imagePath, PATHINFO_FILENAME);
            $name = preg_replace('/-row-\d+$/', '', $filename);
            $nameKey = Str::slug($name ?? '');

            if ($nameKey !== '') {
                $imagesByName[$nameKey][] = basename($imagePath);
            }
        }

        $stakeholders = Stakeholder::query()
            ->where(function ($query) {
                $query->whereNull('avatar')->orWhere('avatar', '');
            })
            ->get(['id', 'name']);

        $stakeholdersByName = $stakeholders->groupBy(fn (Stakeholder $stakeholder) => Str::slug($stakeholder->name));
        $updated = 0;
        $ambiguous = [];
        $unmatched = [];

        foreach ($stakeholdersByName as $nameKey => $matchingStakeholders) {
            if ($nameKey === '' || ! isset($imagesByName[$nameKey])) {
                foreach ($matchingStakeholders as $stakeholder) {
                    $unmatched[] = $stakeholder->name;
                }

                continue;
            }

            // Never guess when names or image files collide.
            if ($matchingStakeholders->count() !== 1 || count($imagesByName[$nameKey]) !== 1) {
                $ambiguous[] = $matchingStakeholders->pluck('name')->unique()->implode(', ');
                continue;
            }

            $stakeholder = $matchingStakeholders->first();
            $avatarPath = self::IMAGE_DIRECTORY . '/' . $imagesByName[$nameKey][0];

            // Recheck the blank value in the update query in case it changed
            // after the initial selection. Query builder bypasses model fillable.
            $changed = DB::table('stakeholders')
                ->where('id', $stakeholder->id)
                ->where(function ($query) {
                    $query->whereNull('avatar')->orWhere('avatar', '');
                })
                ->update(['avatar' => $avatarPath]);

            $updated += $changed;
        }

        $this->command?->info("NEC stakeholder avatars added: {$updated}");
        $this->command?->info('Skipped without a matching downloaded image: ' . count($unmatched));

        if ($ambiguous !== []) {
            $this->command?->warn('Skipped ambiguous matches: ' . implode('; ', $ambiguous));
        }

        if ($unmatched !== []) {
            $this->command?->line('No image match: ' . implode('; ', $unmatched));
        }
    }
}
