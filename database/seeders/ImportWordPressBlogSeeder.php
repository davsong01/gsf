<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class ImportWordPressBlogSeeder extends Seeder
{
    public function run(): void
    {
        $file = public_path('uploads/gofamintstudents039fellowship.WordPress.2026-11-03.xml');

        if (!is_file($file)) {
            throw new RuntimeException("WordPress export file not found: {$file}");
        }

        $status = Artisan::call('blog:import-wordpress', [
            'file' => $file,
            '--no-media' => true,
        ]);
        $this->command?->line(Artisan::output());

        if ($status !== 0) {
            throw new RuntimeException('WordPress blog import failed.');
        }
    }
}
