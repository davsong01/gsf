<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image as Image;

class FileUploadService
{
    public function secureUpload(
        UploadedFile $file,
        string $folder = 'signatures',
        ?string $oldFile = null,
        ?string $filename = null
    ): string {
        $disk = 'protected_uploads';
        $filename = $filename ?: $this->generateFilename($file);
        $folder = trim('uploads/'.$folder, '/');

        $path = "{$folder}/{$filename}";

        // Delete old file if exists (expects relative path)
        if ($oldFile && Storage::disk($disk)->exists(base64_decode($oldFile))) {
            Storage::disk($disk)->delete(base64_decode($oldFile));
        }

        // Ensure folder exists
        if (!Storage::disk($disk)->exists($folder)) {
            Storage::disk($disk)->makeDirectory($folder);
        }
        
        Storage::disk($disk)->putFileAs($folder, $file, $filename);

        return base64_encode($path);
    }


    public function publicUpload(UploadedFile $file, string $folder = 'uploads', string $oldFile = '', $filename = false): string
    {
        $folder = 'uploads/'.$folder;

        $folderPath = public_path(trim($folder, '/')); // e.g., public/uploads
        $filename   = $filename ?? $file->getClientOriginalName();

        // Ensure folder exists
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Delete old file if exists
        if ($oldFile && file_exists(public_path($oldFile))) {
            unlink(public_path($oldFile));
        }

        // Move file to public folder
        $file->move($folderPath, $filename);

        return asset(trim($folder, '/') . '/' . $filename);
    }

    /**
     * Generate a unique filename with timestamp and random string.
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return Str::random(8) . '.' . $extension;
    }

    public function uploadImage($image, $location, $width = null, $height = null)
    {
        $location = 'uploads/'.$location; // lets have them in one folder henceforth

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

    public function serveProtectedFile(string $encodedPath)
    {
        $disk = 'protected_uploads';

        // Decode base64 path
        $decoded = base64_decode($encodedPath, true);

        if ($decoded === false || str_contains($decoded, '..') || trim($decoded) === '') {
            abort(403, 'Invalid file path.');
        }

        if (!Storage::disk($disk)->exists($decoded)) {
            abort(404, 'File not found.');
        }

        $absolutePath = Storage::disk($disk)->path($decoded);
        $mimeType = Storage::disk($disk)->mimeType($decoded) ?? 'application/octet-stream';
        $fileName = basename($decoded);

        // Inline for images/PDF, download otherwise
        $inlineTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $disposition = in_array($mimeType, $inlineTypes) ? 'inline' : 'attachment';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "{$disposition}; filename=\"{$fileName}\""
        ]);
    }

    public static function secureFileUrl(string $relativePath): string
    {
        return route('protected.download', ['file' => base64_encode($relativePath)]);
    }

}
