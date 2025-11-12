<?php

namespace App\Services;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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

    public static function uploadImage($image, $location, $width = null, $height = null)
    {
        // Make sure the directory exists
        if (!file_exists($location)) {
            mkdir($location, 0755, true); // recursive mkdir with permissions
        }

        $imgName = time() . rand(11111111, 9999999) . '.' . $image->getClientOriginalExtension();

        // Resize if width and height are provided
        if ($width && $height) {
            $image = Image::make($image)->resize($width, $height);
        } else {
            $image = Image::make($image);
        }

        $image->save($location . '/' . $imgName);

        return $location . '/' . $imgName;
    }
}
