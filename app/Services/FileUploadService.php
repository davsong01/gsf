<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class FileUploadService
{
    public static function secureUpload(UploadedFile $file, string $folder = 'signatures', string $oldFile = null): string
    {
        $disk = 'protected_uploads';
        $filename = self::generateFilename($file);
        $path = trim($folder, '/') . '/' . $filename;

        // Delete old file if exists
        if ($oldFile && Storage::disk($disk)->exists($oldFile)) {
            Storage::disk($disk)->delete($oldFile);
        }

        // Ensure folder exists
        if (!Storage::disk($disk)->exists($folder)) {
            Storage::disk($disk)->makeDirectory($folder);
        }

        // Save file
        Storage::disk($disk)->putFileAs($folder, $file, $filename);
        
        // Encode relative path for secure URL
        $encodedPath = base64_encode($path);

        // Return route to access the file
        return route('protected.download', ['file' => $encodedPath]);
    }


    /**
     * Generate a unique filename with timestamp and random string.
     */
    protected static function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return Str::random(8) . '.' . $extension;
    }
}
