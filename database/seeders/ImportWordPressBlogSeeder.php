<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class ImportWordPressBlogSeeder extends Seeder
{
    public function run(): void
    {
        $file = config('blog.wordpress_wxr_path');

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
