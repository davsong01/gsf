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
    // public function index(Request $request, $user, $type, $isAdmin)
    // {
    //     $role = $user->role_id ?? $user->role;
        
    //     /** =====================
    //      * BASE MODELS
    //      * ===================== */
    //     $chaptersQuery = Chapter::query();
    //     $zonesQuery = Zone::query();
    //     $fieldsQuery = Field::query();

    //     $chapterIds = collect();
    //     $zoneIds = collect();
    //     $fieldIds = collect();

    //     /** =====================
    //      * ROLE-BASED SCOPING
    //      * ===================== */
    //     if ($isAdmin || finStakeholders($user)) {
    //         // Admin → full access
    //         $chapterIds = Chapter::pluck('id');
    //         $zoneIds    = Zone::pluck('id');
    //         $fieldIds   = Field::pluck('id');
    //     } else {
    //         if (in_array($role, chapterStakeholders())) {
    //             $chapterIds = collect([$user->chapter_id]);
    //             $zoneIds    = collect([$user->zone_id]);
    //             $fieldIds   = collect([$user->field_id]);
    //         }
    //         elseif (in_array($role, zoneStakeholders())) {
    //             $zoneIds = collect([$user->zone_id]);

    //             $chapterIds = Chapter::where('zone_id', $user->zone_id)->pluck('id');

    //             $fieldIds = Field::whereHas('zones', fn ($q) =>
    //                 $q->where('id', $user->zone_id)
    //             )->pluck('id');
    //         }
    //         elseif (in_array($role, fieldStakeholders())) {
    //             $fieldIds = collect([$user->field_id]);

    //             $zoneIds = Zone::where('field_id', $user->field_id)->pluck('id');

    //             $chapterIds = Chapter::whereIn('zone_id', $zoneIds)->pluck('id');
    //         }
    //         elseif (in_array($role, secretariatStakeholders())) {
    //             $chapterIds = Chapter::pluck('id');
    //             $zoneIds    = Zone::pluck('id');
    //             $fieldIds   = Field::pluck('id');
    //         }
    //     }

    //     // Fixed initialization syntax order: Model::query()->with(...)
    //     $awards = Award::query()->with('entries', 'chapter', 'field', 'zone')
    //         ->when($request->filled('reference'), fn ($q) => $q->where('reference', $request->reference))
    //         ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
    //         ->when($chapterIds->isNotEmpty(), fn ($q) => $q->whereIn('chapter_id', $chapterIds))
    //         ->when($zoneIds->isNotEmpty(), fn ($q) => $q->whereIn('zone_id', $zoneIds))
    //         ->when($fieldIds->isNotEmpty(), fn ($q) => $q->whereIn('field_id', $fieldIds));

    //     /** =====================
    //      * DATE RANGE FILTERS
    //      * ===================== */
    //     if ($request->filled('from_date') || $request->filled('to_date')) {
    //         // Fallback boundaries if only one input is present
    //         $from = $request->filled('from_date')
    //             ? Carbon::parse($request->from_date)->startOfDay()
    //             : Carbon::parse('1970-01-01')->startOfDay();

    //         $to = $request->filled('to_date')
    //             ? Carbon::parse($request->to_date)->endOfDay()
    //             : Carbon::now()->endOfDay();

    //         // Standard Laravel whereBetween query implementation
    //         $awards->whereBetween('created_at', [$from, $to]);
    //     }

    //     /** =====================
    //      * SCOPE FILTERS
    //      * ===================== */
    //     foreach (['chapter', 'zone', 'field'] as $scope) {
    //         if ($request->filled("{$scope}_filter")) {
    //             $awards->where("{$scope}_id", $request->input("{$scope}_filter"));
    //         }
    //     }

    //     /** =====================
    //      * STATUS FILTERS
    //      * ===================== */
    //     if ($request->filled('status_filter')) {
    //         $statusMap = [
    //             'field_pending'      => ['field_status', 0],
    //             'field_approved'     => ['field_status', 1],
    //             'field_rejected'     => ['field_status', 2],
    //             'zone_pending'       => ['zone_status', 0],
    //             'zone_approved'      => ['zone_status', 1],
    //             'zone_rejected'      => ['zone_status', 2],
    //             'national_pending'   => ['national_status', 0],
    //             'national_approved'  => ['national_status', 1],
    //             'national_rejected'  => ['national_status', 2],
    //         ];

    //         if (isset($statusMap[$request->status_filter])) {
    //             [$column, $value] = $statusMap[$request->status_filter];
    //             $awards->where($column, $value);
    //         }
    //     }

    //     return [
    //         'awards'   => $awards->with(['chapter', 'zone', 'field'])
    //                             ->orderByDesc('created_at') // Organized chronological timeline sorting
    //                             ->paginate(20),
    //         'chapters' => $chaptersQuery->whereIn('id', $chapterIds)->orderBy('name')->get(),
    //         'zones'    => $zonesQuery->whereIn('id', $zoneIds)->orderBy('name')->get(),
    //         'fields'   => $fieldsQuery->whereIn('id', $fieldIds)->orderBy('name')->get(),
    //     ];
    // }
    // public function index(Request $request, $user, $type, $isAdmin)
    // {
    //     $role = $user->role_id ?? $user->role;
        
    //     /** =====================
    //      * BASE MODELS
    //      * ===================== */
    //     $chaptersQuery = Chapter::query();
    //     $zonesQuery = Zone::query();
    //     $fieldsQuery = Field::query();

    //     $chapterIds = collect();
    //     $zoneIds = collect();
    //     $fieldIds = collect();

    //     /** =====================
    //      * ROLE-BASED SCOPING
    //      * ===================== */
    //     if ($isAdmin || finStakeholders($user)) {
    //         // Admin → full access
    //         $chapterIds = Chapter::pluck('id');
    //         $zoneIds    = Zone::pluck('id');
    //         $fieldIds   = Field::pluck('id');
    //     } else {
    //         if (in_array($role, chapterStakeholders())) {
    //             $chapterIds = collect([$user->chapter_id]);
    //             $zoneIds    = collect([$user->zone_id]);
    //             $fieldIds   = collect([$user->field_id]);
    //         }
    //         elseif (in_array($role, zoneStakeholders())) {
    //             $zoneIds = collect([$user->zone_id]);
    //             $chapterIds = Chapter::where('zone_id', $user->zone_id)->pluck('id');
    //             $fieldIds = Field::whereHas('zones', fn ($q) =>
    //                 $q->where('id', $user->zone_id)
    //             )->pluck('id');
    //         }
    //         elseif (in_array($role, fieldStakeholders())) {
    //             $fieldIds = collect([$user->field_id]);
    //             $zoneIds = Zone::where('field_id', $user->field_id)->pluck('id');
    //             $chapterIds = Chapter::whereIn('zone_id', $zoneIds)->pluck('id');
    //         }
    //         elseif (in_array($role, secretariatStakeholders())) {
    //             $chapterIds = Chapter::pluck('id');
    //             $zoneIds    = Zone::pluck('id');
    //             $fieldIds   = Field::pluck('id');
    //         }
    //     }

    //     // Build the query scope constraints
    //     $awardsQuery = Award::query()->with(['entries', 'chapter', 'field', 'zone'])
    //         ->when($request->filled('reference'), fn ($q) => $q->where('reference', $request->reference))
    //         ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
    //         ->when($chapterIds->isNotEmpty(), function ($q) use ($chapterIds) {
    //             // Include assigned chapters OR anything with a null chapter context if roles permit
    //             $q->where(fn($sub) => $sub->whereIn('chapter_id', $chapterIds)->orWhereNull('chapter_id'));
    //         })
    //         ->when($zoneIds->isNotEmpty(), fn ($q) => $q->whereIn('zone_id', $zoneIds))
    //         ->when($fieldIds->isNotEmpty(), fn ($q) => $q->whereIn('field_id', $fieldIds));

    //     /** =====================
    //      * DATE RANGE FILTERS
    //      * ===================== */
    //     if ($request->filled('from_date') || $request->filled('to_date')) {
    //         $from = $request->filled('from_date')
    //             ? \Carbon\Carbon::parse($request->from_date)->startOfDay()
    //             : \Carbon\Carbon::parse('1970-01-01')->startOfDay();

    //         $to = $request->filled('to_date')
    //             ? \Carbon\Carbon::parse($request->to_date)->endOfDay()
    //             : \Carbon\Carbon::now()->endOfDay();

    //         $awardsQuery->whereBetween('created_at', [$from, $to]);
    //     }

    //     /** =====================
    //      * SCOPE FILTERS
    //      * ===================== */
    //     foreach (['chapter', 'zone', 'field'] as $scope) {
    //         if ($request->filled("{$scope}_filter")) {
    //             $awardsQuery->where("{$scope}_id", $request->input("{$scope}_filter"));
    //         }
    //     }

    //     /** =====================
    //      * STATUS FILTERS
    //      * ===================== */
    //     if ($request->filled('status_filter')) {
    //         $statusMap = [
    //             'field_pending'      => ['field_status', 0],
    //             'field_approved'     => ['field_status', 1],
    //             'field_rejected'     => ['field_status', 2],
    //             'zone_pending'       => ['zone_status', 0],
    //             'zone_approved'      => ['zone_status', 1],
    //             'zone_rejected'      => ['zone_status', 2],
    //             'national_pending'   => ['national_status', 0],
    //             'national_approved'  => ['national_status', 1],
    //             'national_rejected'  => ['national_status', 2],
    //         ];

    //         if (isset($statusMap[$request->status_filter])) {
    //             [$column, $value] = $statusMap[$request->status_filter];
    //             $awardsQuery->where($column, $value);
    //         }
    //     }

    //     // Execute results fetching pagination mapping
    //     $paginatedAwards = $awardsQuery->orderByDesc('created_at')->paginate(100);
        
    //     $groupedAwards = $paginatedAwards->getCollection()->groupBy(function ($award) {
    //         // Condition cluster checks: if chapter_id doesn't exist, dump into a single unified key group
    //         if (empty($award->chapter_id)) {
    //             return 'No Assigned Chapter';
    //         }

    //         // Otherwise generate unique tracking group identifier keys
    //         $chapStr  = $award->chapter->name ?? 'Unmapped Chapter';

    //         return "{$chapStr}";
    //     });

    //     // Replace default flat items inside pagination with our grouped collection list tree framework
    //     $paginatedAwards->setCollection($groupedAwards);
    
    //     return [
    //         'awards'   => $paginatedAwards, // Returns grouped structural hierarchy maintaining page offsets links!
    //         'chapters' => $chaptersQuery->whereIn('id', $chapterIds)->orderBy('name')->get(),
    //         'zones'    => $zonesQuery->whereIn('id', $zoneIds)->orderBy('name')->get(),
    //         'fields'   => $fieldsQuery->whereIn('id', $fieldIds)->orderBy('name')->get(),
    //     ];
    // }
    public function index(Request $request, $user, $type, $isAdmin)
{
    $role = $user->role_id ?? $user->role;
    
    /** =====================
     * BASE MODELS
     * ===================== */
    $chaptersQuery = Chapter::query();
    $zonesQuery = Zone::query();
    $fieldsQuery = Field::query();

    $chapterIds = collect();
    $zoneIds = collect();
    $fieldIds = collect();

    /** =====================
     * ROLE-BASED SCOPING
     * ===================== */
    if ($isAdmin || finStakeholders($user)) {
        // Admin / Fin Stakeholders → full access
        $chapterIds = Chapter::pluck('id');
        $zoneIds    = Zone::pluck('id');
        $fieldIds   = Field::pluck('id');
    } else {
        if (in_array($role, chapterStakeholders())) {
            $chapterIds = collect([$user->chapter_id]);
            $zoneIds    = collect([$user->zone_id]);
            $fieldIds   = collect([$user->field_id]);
        }
        elseif (in_array($role, zoneStakeholders())) {
            $zoneIds = collect([$user->zone_id]);
            $chapterIds = Chapter::where('zone_id', $user->zone_id)->pluck('id');
            $fieldIds = Field::whereHas('zones', fn ($q) =>
                $q->where('id', $user->zone_id)
            )->pluck('id');
        }
        elseif (in_array($role, fieldStakeholders())) {
            $fieldIds = collect([$user->field_id]);
            $zoneIds = Zone::where('field_id', $user->field_id)->pluck('id');
            $chapterIds = Chapter::whereIn('zone_id', $zoneIds)->pluck('id');
        }
        elseif (in_array($role, secretariatStakeholders())) {
            $chapterIds = Chapter::pluck('id');
            $zoneIds    = Zone::pluck('id');
            $fieldIds   = Field::pluck('id');
        }
    }

    /** =====================
     * BUILD QUERY & SCOPE BOUNDARIES
     * ===================== */
    $awardsQuery = Award::query()->with(['entries', 'chapter', 'field', 'zone'])
        ->when($request->filled('reference'), fn ($q) => $q->where('reference', $request->reference))
        ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type));

    // Unified Location Constraint Framework (Fixes missing null chapter_id rows)
    $awardsQuery->where(function($query) use ($chapterIds, $zoneIds, $fieldIds, $isAdmin, $role, $user) {
        
        // Logical Block 1: Strict operational relationships matching active user nodes
        $query->where(function($sub) use ($chapterIds, $zoneIds, $fieldIds) {
            if ($chapterIds->isNotEmpty()) {
                $sub->whereIn('chapter_id', $chapterIds);
            }
            if ($zoneIds->isNotEmpty()) {
                $sub->whereIn('zone_id', $zoneIds);
            }
            if ($fieldIds->isNotEmpty()) {
                $sub->whereIn('field_id', $fieldIds);
            }
        });

        // Logical Block 2: Fallback bypass for completely unassigned chapters (Global roles only)
        if ($isAdmin || in_array($role, array_merge(secretariatStakeholders(), finStakeholders($user) ? [$role] : []))) {
            $query->orWhereNull('chapter_id');
        }
    });

    /** =====================
     * DATE RANGE FILTERS
     * ===================== */
    if ($request->filled('from_date') || $request->filled('to_date')) {
        $from = $request->filled('from_date')
            ? \Carbon\Carbon::parse($request->from_date)->startOfDay()
            : \Carbon\Carbon::parse('1970-01-01')->startOfDay();

        $to = $request->filled('to_date')
            ? \Carbon\Carbon::parse($request->to_date)->endOfDay()
            : \Carbon\Carbon::now()->endOfDay();

        $awardsQuery->whereBetween('created_at', [$from, $to]);
    }

    /** =====================
     * SCOPE FILTERS (Explicit User Requests)
     * ===================== */
    foreach (['chapter', 'zone', 'field'] as $scope) {
        if ($request->filled("{$scope}_filter")) {
            $awardsQuery->where("{$scope}_id", $request->input("{$scope}_filter"));
        }
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
     * PAGINATION & GROUPING PIPELINE
     * ===================== */
    $paginatedAwards = $awardsQuery->orderByDesc('created_at')->paginate(100);
    
    $groupedAwards = $paginatedAwards->getCollection()
        ->groupBy(function ($award) {
            if (empty($award->chapter_id)) {
                return 'No Official Chapter';
            }

            return $award->chapter->name ?? 'Unmapped Chapter';
        })
        // Sort the groups so 'No Official Chapter' is pushed to the very end
        ->sortBy(function ($value, $key) {
            return $key === 'No Official Chapter' ? 1 : 0;
        });

    // Re-inject the sorted, grouped framework back into the pagination instance
    $paginatedAwards->setCollection($groupedAwards);

    return [
        'awards'   => $paginatedAwards,
        'chapters' => $chaptersQuery->whereIn('id', $chapterIds)->orderBy('name')->get(),
        'zones'    => $zonesQuery->whereIn('id', $zoneIds)->orderBy('name')->get(),
        'fields'   => $fieldsQuery->whereIn('id', $fieldIds)->orderBy('name')->get(),
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
                    }

                    if ($key === 'timestamp') {
                        $award->update([
                            'created_at' => \Carbon\Carbon::parse($value),
                            'updated_at' => \Carbon\Carbon::parse($value)
                        ]);

                        continue;
                    }

                    Log::info([
                        'key' => $key
                    ]);
                    // Handle file processing dynamically via the key string flag
                    if (str_ends_with($key, '_file_id') || in_array($key, ['upload_a_clear_and_recent_picture_of_yourself', 'attach_your_latest_official_school_result_with_your_departments_stamp_and_hod_signature', 'picturesave_picture_as_your_name'])) {
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
