<?php
namespace App\Services;


use App\Models\Award;
use App\Models\AwardEntries;
use App\Services\FileUploadService; // Assuming this is the correct namespace
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

//App ID: AKfycbyn8X8DoQzNh4i0je5U6rN4F1YJ673wemqtMlAnqJkNq11-sGfhvD5ZxGXHNPLkIhRruw
// web app url: https://script.google.com/macros/s/AKfycbyn8X8DoQzNh4i0je5U6rN4F1YJ673wemqtMlAnqJkNq11-sGfhvD5ZxGXHNPLkIhRruw/exec

class AwardService{
    public function storeFromGoogle(Request $request)
    {
        Log::info('Structured Incoming Google Webhook Data:', $request->all());

        $type = $request->input('type') ?? 'Default Google Form';

        // Grab our structured fields array from the request
        $formFields = $request->input('fields', []);

        try {
            DB::transaction(function () use ($formFields, $type) {

                // Create parent record
                $award = Award::create([
                    'type'            => $type,
                    'reference'       => strtoupper($type . '-' . uniqid()),
                    'zone_status'     => 0,
                    'field_status'    => 0,
                    'national_status' => 0,
                ]);

                // Loop through the array of field items
                foreach ($formFields as $field) {
                    $key   = $field['key'] ?? null;
                    $name  = $field['name'] ?? null;
                    $value = $field['value'] ?? null;

                    if (!$key || $value === null || $value === '') {
                        continue;
                    }

                    if ($key === 'select_institution') {
                        // Look up the database to instantly grab the matching ID record
                        $chapter = DB::table('chapters')
                                        ->where('name', $value)
                                        ->first();

                        if ($chapter) {
                            $value = $chapter->id;
                            $key = 'chapter_id';

                            $award->update([
                                'chapter_id' => $chapter->id,
                                'zone_id' => $chapter->zone_id,
                                'field_id' => $chapter->field_id
                            ]);
                        }
                    }

                    // Handle file processing dynamically via the key string flag
                    if (str_ends_with($key, '_file_id')) {
                        try {
                            $client = new Client();
                            $client->setAuthConfig(storage_path('app/google-credentials.json'));
                            $client->addScope(Drive::DRIVE_READONLY);
                            $driveService = new Drive($client);

                            // 1. Fetch metadata first so we can grab the REAL file name and mime type
                            $fileMetadata = $driveService->files->get($value, ['fields' => 'name, mimeType']);
                            $originalName = $fileMetadata->getName();
                            $mimeType = $fileMetadata->getMimeType();

                            // 2. Download the file stream bytes
                            $response = $driveService->files->get($value, ['alt' => 'media']);
                            $fileContents = $response->getBody()->getContents();

                            // 3. Create a temporary file path on your server
                            $tmpFilePath = tempnam(sys_get_temp_dir(), 'gdrive_');
                            file_put_contents($tmpFilePath, $fileContents);

                            // 4. Construct a legitimate UploadedFile instance out of the temp file
                            $uploadedFile = new \Illuminate\Http\UploadedFile(
                                $tmpFilePath,
                                $originalName,
                                $mimeType,
                                UPLOAD_ERR_OK,
                                true // Set test mode to true so it skips PHP's internal is_uploaded_file() check
                            );

                            // 5. This will now work perfectly with your existing service!
                            $uploadedUrl = app(FileUploadService::class)->secureUpload(
                                $uploadedFile,
                                'award-files'
                            );

                            if (file_exists($tmpFilePath)) {
                                @unlink($tmpFilePath);
                            }

                            $key = str_replace('_file_id', '', $key);
                            $value = $uploadedUrl;

                        } catch (\Exception $e) {
                            // Make sure to clean up the temp file if something crashes mid-download
                            if (isset($tmpFilePath) && file_exists($tmpFilePath)) {
                                @unlink($tmpFilePath);
                            }

                            Log::error("Google File Download Failed for key [{$key}]: " . $e->getMessage());
                            $key = str_replace('_file_id', '', $key);
                            $value = 'Download Failed: ' . $e->getMessage();
                        } catch (\Exception $e) {
                            Log::error("Google File Download Failed for key [{$key}]: " . $e->getMessage());
                            $key = str_replace('_file_id', '', $key);
                            $value = 'Download Failed: ' . $e->getMessage();
                        }
                    }

                    // Store everything cleanly matching your precise mapping schema layout
                    AwardEntries::create([
                        'award_id' => $award->id,
                        'key'      => $key,
                        'name'     => $name,
                        'value'    => is_array($value) ? json_encode($value) : (string)$value,
                    ]);
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Structured entry items processed!'], 201);

        } catch (\Exception $e) {
            Log::error("Google Webhook Transaction Failed: " . $e->getMessage());
            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 500);
        }
    }
}
