<?php
namespace App\Services;


use App\Models\Award;
use App\Models\AwardEntries;
use App\Models\AwardShortlist;
use App\Models\AwardShortlistHistory;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Zone;
use App\Services\FileUploadService; // Assuming this is the correct namespace
use Carbon\Carbon;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;


class AwardService{
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

    public function migrateAwardEntries(): string
    {
        $replacementKeys = $this->replacements();

        $keys = DB::table('award_entries')
            ->distinct()
            ->pluck('key')
            ->map(fn ($key) => $replacementKeys[$key] ?? $key)
            ->unique()
            ->values()
            ->toArray();

        if (! Schema::hasTable('award_entries2')) {

            Schema::create('award_entries2', function (Blueprint $table) use ($keys) {

                $table->id();
                $table->unsignedBigInteger('award_id')->unique();

                foreach ($keys as $key) {
                    $table->text($key)->nullable();
                }

                $table->timestamps();
            });

        } else {

            $existingColumns = Schema::getColumnListing('award_entries2');

            $missingColumns = array_diff(
                $keys,
                $existingColumns
            );

            if (! empty($missingColumns)) {

                Schema::table('award_entries2', function (Blueprint $table) use ($missingColumns) {

                    foreach ($missingColumns as $column) {
                        $table->text($column)->nullable();
                    }
                });
            }
        }

        DB::beginTransaction();

        try {

            $now = now();

            $entries = DB::table('award_entries')
                ->whereNull('migrated_at')
                ->orderBy('award_id')
                ->get()
                ->groupBy('award_id');

            foreach ($entries as $awardId => $records) {

                $row = [
                    'award_id'   => $awardId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $ids = [];

                foreach ($records as $record) {

                    $column = $replacementKeys[$record->key]
                        ?? $record->key;

                    $row[$column] = $record->value;

                    $ids[] = $record->id;
                }

                DB::table('award_entries2')->updateOrInsert(
                    ['award_id' => $awardId],
                    $row
                );

                DB::table('award_entries')
                    ->whereIn('id', $ids)
                    ->update([
                        'migrated_at' => $now,
                        'updated_at'  => $now,
                    ]);
            }

            DB::commit();

            return 'All done';

        } catch (\Throwable $e) {

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            throw $e;
        }
    }

    public function replacements(){
        return
        [
            'give_a_brief_testimony_of_your_salvation_here' => 'salvation_testimony',
            'facultyschool' => 'faculty_name',
            'proposed_degreediploma' => 'proposed_degree_diploma',

            'name_branch_assembly_of_your_localhome_church' => 'local_church_name',

            'phone_number_of_your_local_assembly_pastor' => 'local_pastor_phone_number',

            'your_cumulative_grade_point_average_cgpa' => 'cgpa',

            'enter_your_account_details_note_this_does_not_guarantee_automatic_qualification_its_simply_to_help_streamline_the_process' => 'account_details',

            'upload_a_clear_and_recent_picture_of_yourself' => 'picture',
            'picturesave_picture_as_your_name' => 'picture',

            'enter_institution_if_you_selected_others_above' => 'other_institution_name',

            'home_town_' => 'home_town',

            'permanent_home_address_' => 'permanent_home_address',

            'facultyschool_' => 'faculty_name',

            'course_of_study_' => 'course_of_study',

            'proposed_degreediploma_' => 'proposed_degree_diploma',

            'matric_no_' => 'matric_no',

            'proposed_year_of_completion_' => 'proposed_year_of_completion',

            'name_branch_assembly_of_your_localhome_church_' => 'local_church_name',

            'local_church_address_' => 'local_church_address',

            'first_semester_gpa_'  => 'first_semester_gpa',

            // Miscellaneous
            'name_surname_first' => 'first_name',
            'when' => 'born_again_since',
            'upload_result' => 'result_file',
            'result_file' => 'result_file',
            'attach_your_latest_official_school_result_with_your_departments_stamp_and_hod_signature' => 'result_file'

        ];
    }

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
        $awardsQuery = Award::whereYear('created_at', now()->year)
            ->with([
                'entries',
                'chapter',
                'zone',
                'field',
                'shortlists',
                'currentShortlistStage'
            ]);

        if ($request->filled('current_shortlist_stage_id')) {

            $awardsQuery->where(
                'current_shortlist_stage_id',
                $request->current_shortlist_stage_id
            );
        }

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

        if ($request->filled('name')) {
            $awardsQuery->whereHas('entries', function ($query) use ($request) {
                $query->where('value', 'like', '%' . $request->name . '%');
            });
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
            ->paginate(200);

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

    public function bulkShortlist(array $ids, int $stageId, ?string $remarks = null): void
    {
        if (empty($ids)) {
            throw new \Exception('No entries selected.');
        }

        DB::beginTransaction();

        try {
            $awards = Award::withTrashed()
                ->whereIn('id', $ids)
                ->get();

            foreach ($awards as $award) {

                $award->update([
                    'current_shortlist_stage_id' => $stageId,
                ]);

                // optional: if you already have history table
                $this->logStageChange($award->id, $stageId, $remarks);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    protected function logStageChange(int $awardId, int $stageId, ?string $remarks = null): void
    {
        // placeholder safe guard
        if (!class_exists(AwardShortlist::class)) {
            return;
        }

        AwardShortlist::create([
            'award_id' => $awardId,
            'shortlisted_by' => auth()->Id(),
            'award_shortlist_stage_id' => $stageId,
            'remarks' => $remarks,
        ]);
    }

    // [
    //     0 => "email_address"
    //     1 => "first_name"
    //     2 => "last_name"
    //     3 => "middle_name"
    //     4 => "date_of_birth"
    //     5 => "gender"
    //     6 => "phone_number"
    //     7 => "home_town"
    //     8 => "permanent_home_address"
    //     9 => "chapter_id"
    //     10 => "faculty_school"
    //     11 => "department"
    //     12 => "course_of_study"
    //     13 => "proposed_degree_diploma"
    //     14 => "matric_no"
    //     15 => "current_level"
    //     16 => "proposed_year_of_completion"
    //     17 => "name_of_zonal_pastor"
    //     18 => "are_you_born_again"
    //     19 => "born_again_since"
    //     20 => "give_a_brief_testimony_of_your_salvation_here"
    //     21 => "local_church_name"
    //     22 => "local_church_address"
    //     23 => "name_of_your_local_assembly_pastor"
    //     24 => "local_pastor_phone_number"
    //     25 => "name_of_your_gsf_campus_president"
    //     26 => "name_of_your_gsf_campus_secretary"
    //     27 => "cgpa"
    //     28 => "latest_official_school_result"
    //     29 => "first_semester_gpa"
    //     30 => "second_semester_gpa"
    //     31 => "hods_phone_number"
    //     32 => "account_details"
    //     33 => "picture"
    //     34 => "select_institution"
    //     35 => "home_town_alt"
    //     36 => "faculty_name"
    //     37 => "class_of_degree"
    //     38 => "account_name"
    //     39 => "account_number"
    //     40 => "bank_name"
    //     41 => "result_file"
    //     42 => "other_institution_name"
    // ]
}
