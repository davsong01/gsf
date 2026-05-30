<?php
namespace App\Services;


use App\Models\Award;
use App\Models\AwardEntries;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Zone;
use App\Services\FileUploadService; // Assuming this is the correct namespace
use Carbon\Carbon;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

//App ID: AKfycbyn8X8DoQzNh4i0je5U6rN4F1YJ673wemqtMlAnqJkNq11-sGfhvD5ZxGXHNPLkIhRruw
// web app url: https://script.google.com/macros/s/AKfycbyn8X8DoQzNh4i0je5U6rN4F1YJ673wemqtMlAnqJkNq11-sGfhvD5ZxGXHNPLkIhRruw/exec

class AwardService{
    public function index(Request $request, $user, $type, $isAdmin)
    {
        $role = $user->role_id ?? $user->role;

        /** =====================
         * BASE MODELS
         * ===================== */
        $chaptersQuery = Chapter::query();
        $zonesQuery    = Zone::query();
        $fieldsQuery   = Field::query();

        $chapterIds = collect();
        $zoneIds    = collect();
        $fieldIds   = collect();

        /** =====================
         * ROLE-BASED ACCESS
         * ===================== */
        if ($isAdmin || finStakeholders($user)) {

            // Full access
            $chapterIds = Chapter::pluck('id');
            $zoneIds    = Zone::pluck('id');
            $fieldIds   = Field::pluck('id');

        } else {

            // Chapter Stakeholder
            if (in_array($role, chapterStakeholders())) {

                $chapterIds = collect([$user->chapter_id])->filter();
                $zoneIds    = collect([$user->zone_id])->filter();
                $fieldIds   = collect([$user->field_id])->filter();
            }

            // Zone Stakeholder
            elseif (in_array($role, zoneStakeholders())) {

                $zoneIds = collect([$user->zone_id])->filter();

                $chapterIds = Chapter::where('zone_id', $user->zone_id)
                    ->pluck('id');

                $fieldIds = Zone::where('id', $user->zone_id)
                    ->pluck('field_id');
            }

            // Field Stakeholder
            elseif (in_array($role, fieldStakeholders())) {

                $fieldIds = collect([$user->field_id])->filter();

                $zoneIds = Zone::where('field_id', $user->field_id)
                    ->pluck('id');

                $chapterIds = Chapter::whereIn('zone_id', $zoneIds)
                    ->pluck('id');
            }

            // Secretariat
            elseif (in_array($role, secretariatStakeholders())) {

                $chapterIds = Chapter::pluck('id');
                $zoneIds    = Zone::pluck('id');
                $fieldIds   = Field::pluck('id');
            }
        }

        /** =====================
         * AWARDS QUERY
         * ===================== */
        $awardsQuery = Award::query()
            ->with([
                'entries',
                'chapter',
                'zone',
                'field'
            ]);

        /** =====================
         * ROLE SCOPE
         * ===================== */

        // Chapter Stakeholder
        if (
            !$isAdmin &&
            in_array($role, chapterStakeholders())
        ) {

            $awardsQuery->whereIn(
                'chapter_id',
                $chapterIds
            );
        }

        // Zone Stakeholder
        elseif (
            !$isAdmin &&
            in_array($role, zoneStakeholders())
        ) {

            $awardsQuery->whereIn(
                'zone_id',
                $zoneIds
            );
        }

        // Field Stakeholder
        elseif (
            !$isAdmin &&
            in_array($role, fieldStakeholders())
        ) {

            $awardsQuery->whereIn(
                'field_id',
                $fieldIds
            );
        }

        /** =====================
         * BASIC FILTERS
         * ===================== */

        if ($request->filled('reference')) {

            $awardsQuery->where(
                'reference',
                $request->reference
            );
        }

        if ($request->filled('type')) {

            $awardsQuery->where(
                'type',
                $request->type
            );
        }

        /** =====================
         * LOCATION FILTERS
         * ===================== */

        // Chapter Filter
        if (
            $request->filled('chapter_id') &&
            $chapterIds->contains($request->chapter_id)
        ) {

            $awardsQuery->where(
                'chapter_id',
                $request->chapter_id
            );
        }

        // Zone Filter
        if (
            $request->filled('zone_id') &&
            $zoneIds->contains($request->zone_id)
        ) {

            $awardsQuery->where(
                'zone_id',
                $request->zone_id
            );
        }

        // Field Filter
        if (
            $request->filled('field_id') &&
            $fieldIds->contains($request->field_id)
        ) {

            $awardsQuery->where(
                'field_id',
                $request->field_id
            );
        }

        /** =====================
         * DATE FILTERS
         * ===================== */
        if (
            $request->filled('from_date') ||
            $request->filled('to_date')
        ) {

            $from = $request->filled('from_date')
                ? Carbon::parse($request->from_date)->startOfDay()
                : Carbon::parse('1970-01-01')->startOfDay();

            $to = $request->filled('to_date')
                ? Carbon::parse($request->to_date)->endOfDay()
                : now()->endOfDay();

            $awardsQuery->whereBetween(
                'created_at',
                [$from, $to]
            );
        }

        /** =====================
         * STATUS FILTERS
         * ===================== */
        if ($request->filled('status_filter')) {

            $statusMap = [
                'field_pending'      => ['field_status', 0],
                'field_approved'     => ['field_status', 1],
                'field_rejected'     => ['field_status', 2],

                'zone_pending'       => ['zone_status', 0],
                'zone_approved'      => ['zone_status', 1],
                'zone_rejected'      => ['zone_status', 2],

                'national_pending'   => ['national_status', 0],
                'national_approved'  => ['national_status', 1],
                'national_rejected'  => ['national_status', 2],
            ];

            if (isset($statusMap[$request->status_filter])) {

                [$column, $value] = $statusMap[$request->status_filter];

                $awardsQuery->where($column, $value);
            }
        }

        /** =====================
         * PAGINATION
         * ===================== */
        $paginatedAwards = $awardsQuery
            ->orderByDesc('created_at')
            ->paginate(100);

        /** =====================
         * GROUPING
         * ===================== */
        $groupedAwards = $paginatedAwards->getCollection()
            ->groupBy(function ($award) {

                if (empty($award->chapter_id)) {
                    return 'No Official Chapter';
                }

                return $award->chapter->name ?? 'Unmapped Chapter';
            })
            ->sortBy(function ($value, $key) {

                return $key === 'No Official Chapter'
                    ? 1
                    : 0;
            });

        $paginatedAwards->setCollection($groupedAwards);

        /** =====================
         * RESPONSE
         * ===================== */
        return [

            'awards' => $paginatedAwards,

            'chapters' => $chaptersQuery
                ->whereIn('id', $chapterIds)
                ->orderBy('name')
                ->get(),

            'zones' => $zonesQuery
                ->whereIn('id', $zoneIds)
                ->orderBy('name')
                ->get(),

            'fields' => $fieldsQuery
                ->whereIn('id', $fieldIds)
                ->orderBy('name')
                ->get(),
        ];
    }

    public function storeFromGoogle(array $data)
    {

        $type = $data['type'] ?? 'Default Google Form';

        // Grab our structured fields array from the data array input
        $formFields = $data['fields'] ?? [];

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

                    if (in_array($key, ['column_38'])) {
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
                                'zone_id'    => $chapter->zone_id,
                                'field_id'   => $chapter->field_id
                            ]);
                        }
                    }elseif ($key === 'timestamp') {
                        $award->update([
                            'created_at' => \Carbon\Carbon::parse($value),
                            'updated_at' => \Carbon\Carbon::parse($value)
                        ]);

                        continue;
                    }elseif (str_ends_with($key, '_file_id') || in_array($key, ['upload_a_clear_and_recent_picture_of_yourself', 'attach_your_latest_official_school_result_with_your_departments_stamp_and_hod_signature', 'picturesave_picture_as_your_name', 'upload_result'])) {
                        try {
                            if (filter_var($value, FILTER_VALIDATE_URL)) {

                                // Upgraded regex to cleanly extract exactly 28-57 characters of a standard Google Drive ID
                                // without picking up trailing parameters like /view, ?usp=sharing, etc.
                                preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]{28,57})/', $value, $matches);

                                if (!empty($matches[1])) {
                                    $value = $matches[1];
                                } else {
                                    // If it's a URL but NOT a Google Drive link, skip the API entirely
                                    // and just keep the URL as the text value so data isn't lost.
                                    $key = str_replace('_file_id', '', $key);
                                    continue;
                                }
                            }

                            // if (filter_var($value, FILTER_VALIDATE_URL)) {
                            //     preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]{25,})/', $value, $matches);

                            //     if (!empty($matches[1])) {
                            //         // Transform the full URL string into just the clean alphanumeric Drive ID
                            //         $value = $matches[1];
                            //     } else {
                            //         throw new \Exception("Could not extract a valid Google Drive File ID from URL: {$value}");
                            //     }

                            // }
                            // ==========================================
                            // LIVE GOOGLE FORMS: GOOGLE DRIVE API ACCESS
                            // ==========================================
                            $client = new Client();
                            $client->setAuthConfig(storage_path('app/google-credentials.json'));
                            $client->addScope(Drive::DRIVE_READONLY);
                            $driveService = new Drive($client);

                            // 1. Fetch metadata first to get original name & mimeType
                            $fileMetadata = $driveService->files->get($value, ['fields' => 'name, mimeType']);
                            $originalName = $fileMetadata->getName();
                            $mimeType = $fileMetadata->getMimeType();

                            // 2. Stream download the file bytes from Drive
                            $response = $driveService->files->get($value, ['alt' => 'media']);
                            $fileContents = $response->getBody()->getContents();

                            // 3. Keep memory clean by saving to a temp file path
                            $tmpFilePath = tempnam(sys_get_temp_dir(), 'gdrive_');
                            file_put_contents($tmpFilePath, $fileContents);

                            // 4. Instantiate object for FileUploadService validation parameters
                            $uploadedFile = new \Illuminate\Http\UploadedFile(
                                $tmpFilePath,
                                $originalName,
                                $mimeType,
                                UPLOAD_ERR_OK,
                                true
                            );

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
                            if (isset($tmpFilePath) && file_exists($tmpFilePath)) {
                                @unlink($tmpFilePath);
                            }

                            Log::error("File Ingestion processing failed for key [{$key}]: " . $e->getMessage());
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

            return ['status' => 'success', 'message' => 'Structured entry items processed!', 'code' => 201];

        } catch (\Exception $e) {
            Log::error("Google Webhook Transaction Failed: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage(), 'code' => 500];
        }
    }
}
