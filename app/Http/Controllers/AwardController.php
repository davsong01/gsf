<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\AwardEntries;
use App\Models\AwardSetting;
use App\Models\AwardShortlistStage;
use App\Models\Chapter;
use App\Services\AwardService;
use App\Services\EmailService;
use App\Services\ExcelService;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Log;
use ZipArchive;

class AwardController extends Controller
{

    // 2. Type-hint the service in the constructor
    public function __construct(protected AwardService $awardService)
    {
    }

    public function webhook(Request $request){
        Log::info('Raw Incoming Google Webhook Data:', $request->all());

        $this->awardService->storeFromGoogle($request->all());

        return response()->json([], 200);
    }

    public function syncAsset($id){
        $entry = AwardEntries::find($id);
        dd($entry);
    }
    /**
     * Display a listing of the resource.
     */

    public function goAwardEntries(Request $request){
        $adminDetails = isAdmin();
        $isAdmin = $adminDetails['status'];
        $user = $adminDetails['user'];
        $type = 'go';

        $request->merge([
            'type' => $type
        ]);

        $entries = $this->awardService->index($request, $user, $type, $isAdmin);
        $shortlistStages = AwardShortlistStage::where('active', 1)
            ->orderBy('position')
            ->get();

        $title = 'General Overseer (G.O.) Award Submissions';
        return view('admin.awards.index', array_merge(compact('isAdmin','entries', 'user', 'title', 'type', 'shortlistStages')));
    }

    public function etfAwardEntries(Request $request){
        $adminDetails = isAdmin();
        $isAdmin = $adminDetails['status'];
        $user = $adminDetails['user'];

        $type = 'etf';

        $request->merge([
            'type' => $type
        ]);

        $entries = $this->awardService->index($request, $user, $type , $isAdmin);
        $title = 'EducationTrust Fund (E.T.F.) Award Submissions';
        $shortlistStages = AwardShortlistStage::where('active', 1)
            ->orderBy('position')
            ->get();

        return view('admin.awards.index', array_merge(compact('isAdmin','entries', 'user', 'title', 'type', 'shortlistStages')));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Award $award)
    {
        $adminDetails = isAdmin();
        $isAdmin = $adminDetails['status'];
        $user = $adminDetails['user'];

        $award->load('entries');
        $chapters = Chapter::select('id', 'name')->get();
        $shortlistStages = AwardShortlistStage::where('active', 1)
            ->orderBy('position')
            ->get();

        return view('admin.awards.show', compact('isAdmin', 'isAdmin', 'award', 'chapters', 'user', 'shortlistStages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Award $award)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Award $award)
    {
        //
    }

    public function updateAward(Request $request)
    {
        $award = Award::with('entries')->where('id', $request->award_id)->first();

        if (!$award) {
            return back()->with('error', 'Award record not found.');
        }

        $permissions = resolveAwardPermissions($award);

        $canComment = $permissions->canComment;

        if (!$permissions->canEdit && !$permissions->canComment) {
            return back()->with('error', 'Invalid Action: You do not have permission to modify this form.');
        }

        // 3. Establish lookup assets
        $fileFields = fileFields();
        $entries = $request->entries;

        $adminDetails = isAdmin();
        $isAdmin = $adminDetails['status'];
        $userRole = $adminDetails['userRole'];

        // dd($entries, $request->all());
        try {
            if($isAdmin){
                $pdates = $request->only(["chapter_comment", "zone_comment", "field_comment", "national_comment"]);
            }

            if(in_array($userRole, chapterStakeholders()) && $canComment){
                $pdates = $request->only(["chapter_comment"]);
            }

            if(in_array($userRole, zoneStakeholders())  && $canComment){
                $pdates = $request->only(["zone_comment"]);
            }

            if(in_array($userRole, fieldStakeholders())  && $canComment){
                $pdates = $request->only(["field_comment"]);
            }

            if(!empty($pdates)){
                $award->update($pdates);
            }

            if(!empty($entries)){
                foreach ($entries as $key => $value) {
                    // Find the matching entry row relative to this award
                    $toUpdate = $award->entries->firstWhere('key', $key);

                    if (!$toUpdate) {
                        continue;
                    }

                    $cleanKey = strtolower($key);

                    // Check if the current payload key maps to a recognized file type
                    if (in_array($cleanKey, $fileFields) || str_contains($cleanKey, 'file') || str_contains($cleanKey, 'image')) {

                        // Ensure a physical file asset was actually attached inside the request payload
                        if ($request->hasFile("entries.{$key}")) {
                            $uploadedFile = $request->file("entries.{$key}");

                            // Validate file integrity before shipping to the upload pipeline
                            if ($uploadedFile->isValid()) {

                                // Execute your secure upload service structure
                                $value = app(FileUploadService::class)->secureUpload(
                                    $uploadedFile,
                                    'award-files'
                                );

                                // Grab the temp file path from the upload to handle cleaning downstream
                                $tmpFilePath = $uploadedFile->getRealPath();
                                if ($tmpFilePath && file_exists($tmpFilePath)) {
                                    @unlink($tmpFilePath);
                                }
                            }
                        } else {
                            // Critical Safe Fallback: If no new file was uploaded, bypass rewriting
                            // the field so we don't overwrite the existing filename with a null/empty string.
                            continue;
                        }
                    }

                    if(isAdmin()['status']){
                        if(in_array($cleanKey, ['chapter_id', 'select_institution'])){
                            $chapter = Chapter::where('id', $value)->first();
                            if($chapter){
                                $award->update([
                                    'chapter_id' => $chapter->id,
                                    'zone_id' => $chapter->zone_id,
                                    'field_id' => $chapter->field_id,
                                ]);
                            }
                        }
                    }

                    // Corrected: Set the database attribute column 'value' as the key wrapper target
                    $toUpdate->update([
                        'value' => $value,
                    ]);
                }
            }

            return back()->with('message', 'Award entries updated successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'An error occurred while saving records: ' . $e->getMessage());
        }
    }


    public function approveEntry(Request $request, Award $award)
    {
        $user = auth()->user() ?? auth()->guard('stakeholder')->user();
        $userRole = (int)($user->role_id ?? $user->role ?? 0);
        $comment = trim((string) $request->input('comment'));

        $isAdmin = isAdmin()['status'];

        $updateData = [];

        if (in_array($userRole, chapterStakeholders())) {
            $updateData['chapter_status'] = 1;

            if (!empty($comment)) {
                $updateData['chapter_comment'] = $comment;
            }
        }

        if (in_array($userRole, zoneStakeholders())) {
            $updateData['zone_status'] = 1;

            if (!empty($comment)) {
                $updateData['zone_comment'] = $comment;
            }
        }

        if (in_array($userRole, fieldStakeholders())) {
            $updateData['field_status'] = 1;

            if (!empty($comment)) {
                $updateData['field_comment'] = $comment;
            }
        }

        if ($isAdmin) {
            $updateData['national_status'] = 1;
            $updateData['national_approved_on'] = now();
            $updateData['national_approved_by'] = auth()->user()->id;

            if (!empty($comment)) {
                $updateData['national_comment'] = $comment;
            }
        }

        if (!empty($updateData)) {
            $award->update($updateData);
        }

        if ($isAdmin) {
            $awardType = ($award->type === 'go') ? 'First Class' : 'E.T.F.';

            $content = "Dear " . $award->name . ",<br><br>";
            $content .= "Congratulations! We are pleased to inform you that your application for the G.S.F. <strong>{$awardType}</strong> award has been approved by the administration.<br><br>";

            if (!empty($comment)) {
                $content .= "<strong>Reviewer Feedback / Comments:</strong><br>";
                $content .= "<blockquote>" . nl2br(e($comment)) . "</blockquote><br>";
            }

            $content .= "Further updates regarding presentation ceremonies or distributions will be communicated to you shortly.<br><br>";
            $content .= "Best regards,<br>Committee on ETF and First Class Awards";

            $emailsToQueue = [
                'recipient'  => $award->email,
                'subject'    => "Congratulations! Your {$awardType} Entry Has Been Approved",
                'content'    => $content,
                'type'       => 'generic',
                'priority'   => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            EmailService::logEmail($emailsToQueue);
        }

        return back()->with('message', 'Operation Successful');
    }

    public function rejectEntry(Request $request, Award $award)
    {
        $user = auth()->user() ?? auth()->guard('stakeholder')->user();
        $userRole = (int)($user->role_id ?? $user->role ?? 0);
        $comment = trim((string) $request->input('rejection_reason'));

        $isAdmin = isAdmin()['status'];

        // Dynamic array to track what needs to be updated in the database
        $updateData = [];

        if (in_array($userRole, chapterStakeholders())) {
            $updateData['chapter_status'] = 2;

            if (!empty($comment)) {
                $updateData['chapter_comment'] = $comment;
            }
        }

        if (in_array($userRole, zoneStakeholders())) {
            $updateData['zone_status'] = 2;

            if (!empty($comment)) {
                $updateData['zone_comment'] = $comment;
            }
        }

        if (in_array($userRole, fieldStakeholders())) {
            $updateData['field_status'] = 2;

            if (!empty($comment)) {
                $updateData['field_comment'] = $comment;
            }
        }

        if ($isAdmin) {
            $updateData['national_status'] = 2;
            $updateData['national_rejected_on'] = now();
            $updateData['national_rejected_by'] = auth()->user()->id;

            if (!empty($comment)) {
                $updateData['national_comment'] = $comment;
            }
        }

        if (!empty($updateData)) {
            $award->update($updateData);
        }

        if ($isAdmin) {
            $awardType = ($award->type === 'go') ? 'First Class' : 'E.T.F.';

            $content = "Dear ".$award->name.",<br><br>";
            $content .= "We regret to inform you that your application for the G.S.F. <strong>{$awardType}</strong> award has been declined by the administration.<br><br>";

            if (!empty($comment)) {
                $content .= "<strong>Reason / Reviewer Feedback:</strong><br>";
                $content .= "<blockquote>" . nl2br(e($comment)) . "</blockquote><br>";
            } else {
                $content .= "No specific reason was provided by the administration.<br><br>";
            }

            $content .= "Thank you for your participation and interest.<br><br>";
            $content .= "Best regards,<br>Committee on ETF and First Class Awards";

            $emailsToQueue = [
                'recipient'  => $award->email,
                'subject'    => "Update on Your {$awardType} Entry",
                'content'    => $content,
                'type'       => 'generic',
                'priority'   => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            EmailService::logEmail($emailsToQueue);
        }

        return back()->with('message', 'Operation Successful');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Award $award)
    {
        try {
            DB::transaction(function () use ($award) {
                // 1. Delete all associated form input entries first
                $award->entries()->delete();

                // 2. Delete the parent award record
                $award->delete();
            });

            return redirect()->back()->with('message', 'Delete Successful')->with('message', 'Award nomination and all associated entries deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Failed to delete Award ID [{$award->id}]: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while trying to delete the award nomination.');
        }
    }

    public function permanentDelete(Award $award)
    {
        try {
            DB::transaction(function () use ($award) {
                if (method_exists($award->entries(), 'forceDelete')) {
                    $award->entries()->forceDelete();
                } else {
                    $award->entries()->delete();
                }

                $award->forceDelete();
            });

            return redirect()->back()->with('message', 'Award nomination and all associated entries permanently deleted.');

        } catch (\Exception $e) {
            \Log::error("Failed to permanently delete Award ID [{$award->id}]: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while trying to permanently delete the award nomination.');
        }
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No entries selected for bulk approval.');
        }

        try {
            DB::transaction(function () use ($request, $ids) {
                $awards = Award::whereIn('id', $ids)->get();
                foreach ($awards as $award) {
                    $this->approveEntry($request, $award);
                }
            });

            return back()->with('message', 'Selected entries processed for approval.');
        } catch (\Exception $e) {
            \Log::error("Bulk approval loop failure: " . $e->getMessage());
            return back()->with('error', 'An error occurred during bulk approval processing.');
        }
    }

    public function bulkReject(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No entries selected for bulk rejection.');
        }

        try {
            DB::transaction(function () use ($request, $ids) {
                $awards = Award::whereIn('id', $ids)->get();
                foreach ($awards as $award) {
                    $this->rejectEntry($request, $award);
                }
            });

            return back()->with('message', 'Selected entries processed for rejection.');
        } catch (\Exception $e) {
            \Log::error("Bulk rejection loop failure: " . $e->getMessage());
            return back()->with('error', 'An error occurred during bulk rejection processing.');
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No entries selected for deletion.');
        }

        try {
            DB::transaction(function () use ($ids) {
                // If using soft deletes, make sure we can find them normally
                $awards = Award::whereIn('id', $ids)->get();
                foreach ($awards as $award) {
                    // Call your existing single destroy method
                    $this->destroy($award);
                }
            });

            return back()->with('message', 'Selected nominations processed for deletion.');
        } catch (\Exception $e) {
            \Log::error("Bulk deletion loop failure: " . $e->getMessage());
            return back()->with('error', 'An error occurred while deleting the selected nominations.');
        }
    }

    public function bulkPermanentDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No entries selected for permanent purge.');
        }

        try {
            DB::transaction(function () use ($ids) {
                // Use withTrashed() in case entries are already soft-deleted
                $awards = Award::withTrashed()->whereIn('id', $ids)->get();
                foreach ($awards as $award) {
                    // Call your existing single permanentDelete method
                    $this->permanentDelete($award);
                }
            });

            return back()->with('message', 'Selected nominations permanently purged from records.');
        } catch (\Exception $e) {
            \Log::error("Bulk permanent delete loop failure: " . $e->getMessage());
            return back()->with('error', 'An error occurred during permanent bulk execution.');
        }
    }

    public function bulkShortlist(Request $request, AwardService $awardService)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
            'shortlist_stage_id' => ['required', 'integer'],
            'remarks' => ['nullable', 'string'],
        ]);

        try {
            $awardService->bulkShortlist(
                $data['ids'],
                $data['shortlist_stage_id'],
                $data['remarks'] ?? null
            );

            return back()->with('message', 'Selected entries moved to stage successfully.');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            \Log::error("Bulk shortlist failure: " . $e->getMessage());

            return back()->with('error', 'Failed to update shortlist stage.');
        }
    }

    public function awardSettings()
    {
        // Fetches the first record or creates one with defaults if none exist
        $settings = AwardSetting::firstOrCreate(
            ['id' => 1], // Attributes to search for
            [            // Default fallback values if creating for the first time
                'allow_chapter_edit'     => 0,
                'allow_chapter_comment'  => 0,
                'allow_chapter_approval' => 0,
                'allow_zone_edit'        => 0,
                'allow_zone_comment'     => 0,
                'allow_zone_approval'    => 0,
                'allow_field_edit'       => 0,
                'allow_field_comment'    => 0,
                'allow_field_approval'   => 0,
            ]
        );

        return view('admin.awards.settings', compact('settings'));
    }

    public function updateAwardSettings(Request $request){
        $settings = AwardSetting::first();

        $settings->update($request->except(['_token']));

        return redirect()->back()->with('message', 'System configurations saved successfully.');
    }

    public function awardReportsDownload(string $type)
    {
        $awards = Award::where('type', $type)
            ->orderBy('chapter_id', 'asc')
            ->get()
            ->sortBy(function($award) {
                return $award->chapter?->name ?? $award->entries->firstWhere('key', 'select_institution')?->value ?? 'ZZZ';
            });

        $fileName = $type . ' award-nomination-report-' . now()->format('Y-m-d-His') . '.xlsx';

        if ($awards->isEmpty()) {
            return redirect()->back()->with('error', 'No award records found to download.');
        }

        $headers = [
            'Nominee Name', 'Email Address', 'Phone Number', 'Chapter',
            'Chapter Status', 'Zone Status', 'Field Status', 'Final Status',
            'Final Approved By', 'Final Approved On', 'Submission Date'
        ];

        // Helper closure to translate status integers to professional string labels
        $statusLabel = function($val) {
            return match((int)$val) {
                1 => 'Approved',
                2 => 'Rejected',
                default => 'Pending'
            };
        };

        $allRows = [];
        foreach ($awards as $award) {
            // Resolve final approval meta-details safely (assumes national secretariat approval is the final stage)
            $isFinalApproved = ($award->national_status == 1);

            // Lookup final approval tracking trace parameters dynamically
            $finalApprovedBy = $award->approvedBy?->name ?? '';
            $finalApprovedOn = $award->national_approved_on ? Carbon::parse($award->national_approved_on)->format('d M Y, h:i A') : '—';

            $allRows[] = [
                'Nominee Name'      => $award->name ?? 'Unnamed Nominee',
                'Email Address'     => $award->email,
                'Phone Number'      => $award->phone,
                'Chapter'           => $award->chapter->name ?? $award->entries->firstWhere('key', 'select_institution')?->value ?? '—',
                'Chapter Status'    => $statusLabel($award->chapter_status),
                'Zone Status'       => $statusLabel($award->zone_status),
                'Field Status'      => $statusLabel($award->field_status),
                'National Status'   => $statusLabel($award->national_status),
                'Final Approved By' => $finalApprovedBy,
                'Final Approved On' => $finalApprovedOn,
                'Submission Date'   => optional($award->created_at)->format('Y-m-d H:i A'),

                // Raw values appended at the end solely for filtering out separate sheets cleanly
                '_chapter_raw'  => (int)$award->chapter_status,
                '_zone_raw'     => (int)$award->zone_status,
                '_field_raw'    => (int)$award->field_status,
                '_national_raw' => (int)$award->national_status,
            ];
        }

        // 3. Segment Row Arrays into target sheets using internal flags
        // Because the source collection ($allRows) is already perfectly sorted, these sub-collections inherit the same order!
        $sheetsData = [
            'All Nominees'             => collect($allRows),
            'Passed Chapter Clearance' => collect($allRows)->where('_chapter_raw', 1),
            'Passed Zone Clearance'    => collect($allRows)->where('_zone_raw', 1),
            'Passed Field Clearance'   => collect($allRows)->where('_field_raw', 1),
            'Passed National Approval' => collect($allRows)->where('_national_raw', 1),
        ];

        // 4. Clean up internal raw filter data from rows so they don't leak into Excel columns
        foreach ($sheetsData as $sheetName => $collection) {
            $sheetsData[$sheetName] = $collection->map(function ($row) {
                unset($row['_chapter_raw'], $row['_zone_raw'], $row['_field_raw'], $row['_national_raw']);
                return $row;
            })->values()->toArray();
        }

        return ExcelService::downloadMultipleSheets($sheetsData, $headers, $fileName);
    }

    public function awardAssetsDownload()
    {
        // 1. Initial Check to ensure we have data before opening a Zip archive
        $totalAwards = Award::count();
        if ($totalAwards === 0) {
            return redirect()->back()->with('error', 'No award records found to download.');
        }

        // Identify and differentiate asset classification rules
        $allFileFields = fileFields();
        $images = ["picturesave_picture_as_your_name", "upload_a_clear_and_recent_picture_of_yourself"];
        $documents = array_diff($allFileFields, $images);

        // 2. Setup Temporary Zip Target File Archive
        $zipFileName = now()->format('Y-m-d-His') . '-award-assets.zip';

        $zipDirectory = base_path('protected_uploads/award-assets');

        if (!file_exists($zipDirectory)) {
            mkdir($zipDirectory, 0777, true);
        }

        $zipFilePath = $zipDirectory . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Could not initialize compressed ZIP engine context.');
        }

        $zip->addEmptyDir('images');
        $zip->addEmptyDir('documents');

        $hasAddedFiles = false;

        // 3. Expand Execution Limits Safely
        @set_time_limit(300); // Extends script lifetime window up to 5 minutes
        ini_set('memory_limit', '512M'); // Allocates plenty of headspace workspace buffer

        // 4. Stream and download records using memory-safe Chunks
        Award::with('entries')->where('national_status', 1)->latest()->chunk(20, function ($awards) use ($images, $documents, $zip, &$hasAddedFiles) {
            foreach ($awards as $award) {
                $nomineeSlug = str_replace([' ', '/', '\\'], '_', $award->name ?? 'Unnamed_Nominee');

                foreach ($award->entries as $entry) {
                    if (empty($entry->value) || str_starts_with($entry->value, 'Download Failed:')) {
                        continue;
                    }

                    $isImage = in_array($entry->key, $images);
                    $isDocument = in_array($entry->key, $documents);

                    if ($isImage || $isDocument) {
                        try {
                            $decodedPath = base64_decode(ltrim($entry->value, '/'));
                            $fullPath = base_path('protected_uploads/' . ltrim($decodedPath, '/'));

                            if (!file_exists($fullPath)) {
                                Log::warning("Missing file: {$fullPath}");
                                continue;
                            }


                            $sanitizedKey = str_replace(['_file_id', 'upload_a_'], '', $entry->key);

                            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

                            $finalFileName = "{$nomineeSlug}_{$sanitizedKey}.{$extension}";
                            $targetFolder = $isImage ? 'images/' : 'documents/';

                            // Most memory-efficient method
                            $zip->addFile($fullPath, $targetFolder . $finalFileName);

                            $hasAddedFiles = true;

                        } catch (\Exception $e) {
                            dd($e->getMessage());
                            Log::error(
                                "Failed adding asset for Award ID [{$award->id}], Key [{$entry->key}]: " . $e->getMessage()
                            );
                        }
                    }
                }
            }

            // Force PHP to wipe finished Eloquent models from RAM before moving to the next chunk
            unset($awards);
            gc_collect_cycles();
        });

        // Save and finalize our tracking container archive pointers
        $zip->close();

        // 5. Deliver complete file stream to frontend browser
        if ($hasAddedFiles && file_exists($zipFilePath)) {
            return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
        }

        if (file_exists($zipFilePath)) {
            @unlink($zipFilePath);
        }

        return redirect()->back()->with('error', 'No binary attachments were found to include in the ZIP package.');
    }


}
