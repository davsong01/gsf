<?php

namespace App\Errors\Services;

use Illuminate\Support\Facades\File;

class ErrorLogService
{
    protected string $logPath;

    public function __construct()
    {
        $this->logPath = storage_path('logs');
    }

    public function getLogFiles(): array
    {
        if (! File::exists($this->logPath)) {
            return [];
        }

        return collect(File::files($this->logPath))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, 2) . ' KB',
                'modified' => date('Y-m-d H:i', $file->getMTime()),
            ])
            ->values()
            ->toArray();
    }

    public function download(string $file)
    {
        $filePath = $this->pathFor($file);

        if (! File::exists($filePath)) {
            abort(404, 'Log file not found.');
        }

        return response()->download($filePath);
    }

    public function delete(string $file): bool
    {
        $filePath = $this->pathFor($file);

        return File::exists($filePath) && File::delete($filePath);
    }

    public function deleteAll(): void
    {
        if (! File::exists($this->logPath)) {
            return;
        }

        foreach (File::files($this->logPath) as $file) {
            File::delete($file);
        }
    }

    protected function pathFor(string $file): string
    {
        return $this->logPath . '/' . basename($file);
    }
}
