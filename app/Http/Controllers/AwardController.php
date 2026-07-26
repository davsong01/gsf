<?php

namespace App\Http\Controllers;

use App\Models\Award;
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
use Illuminate\Support\Facades\Log;
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
            ->where(function ($query) use ($type) {
                $query->whereNull('award_type')
                    ->orWhere('award_type', 'both')
                    ->orWhere('award_type', $type);
            })
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
            ->where(function ($query) use ($type) {
                $query->whereNull('award_type')
                    ->orWhere('award_type', 'both')
                    ->orWhere('award_type', $type);
            })
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

        $award->load('entry');
        $chapters = Chapter::select('id', 'name')->get();
        $shortlistStages = AwardShortlistStage::where('active', 1)
            ->where(function ($query) use ($award) {
                $query->whereNull('award_type')
                    ->orWhere('award_type', 'both')
                    ->orWhere('award_type', $award->type);
            })
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
        $award = Award::with('entry')->find($request->award_id);

        if (! $award) {
            return back()->with('error', 'Award record not found.');
        }

        $permissions = resolveAwardPermissions($award);

        if (! $permissions->canEdit && ! $permissions->canComment) {
            return back()->with(
                'error',
                'Invalid Action: You do not have permission to modify this form.'
            );
        }

        $adminDetails = isAdmin();
        $isAdmin = $adminDetails['status'];
        $userRole = $adminDetails['userRole'];

        try {
            $awardUpdates = [];

            if ($permissions->canComment) {

                if ($isAdmin) {
                    $awardUpdates = $request->only([
                        'chapter_comment',
                        'zone_comment',
                        'field_comment',
                        'national_comment',
                    ]);
                } elseif (in_array($userRole, chapterStakeholders())) {
                    $awardUpdates = $request->only(['chapter_comment']);
                } elseif (in_array($userRole, zoneStakeholders())) {
                    $awardUpdates = $request->only(['zone_comment']);
                } elseif (in_array($userRole, fieldStakeholders())) {
                    $awardUpdates = $request->only(['field_comment']);
                }

                if (! empty(array_filter($awardUpdates, fn ($value) => filled($value)))) {
                    $award->update($awardUpdates);
                }
            }

            if ($permissions->canEdit) {

                $entryUpdates = [];

                foreach (awardFormFields() as $key => $field) {

                    if (! $request->has("entries.{$key}") && ! $request->hasFile("entries.{$key}")) {
                        continue;
                    }

                    $type = $field['type'];

                    if (in_array($type, ['file', 'image'])) {

                        if (! $request->hasFile("entries.{$key}")) {
                            continue;
                        }

                        $file = $request->file("entries.{$key}");

                        if (! $file->isValid()) {
                            continue;
                        }

                        $entryUpdates[$key] = app(FileUploadService::class)
                            ->secureUpload($file, 'award-files');

                        continue;
                    }

                    $value = $request->input("entries.{$key}");

                    $entryUpdates[$key] = $value;

                    if ($isAdmin && $key === 'chapter_id') {

                        $chapter = Chapter::find($value);

                        if ($chapter) {
                            $award->update([
                                'chapter_id' => $chapter->id,
                                'zone_id' => $chapter->zone_id,
                                'field_id' => $chapter->field_id,
                            ]);
                        }
                    }
                }

                if (! empty($entryUpdates)) {
                    $award->entry->update($entryUpdates);
                }
            }

            return back()->with(
                'message',
                'Award entries updated successfully.'
            );

        } catch (\Throwable $e) {

            report($e);

            return back()->with(
                'error',
                'An error occurred while saving records: '.$e->getMessage()
            );
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
            if (!$award->currentShortlistStage || !$award->currentShortlistStage->stage) {
                return back()->with('error', 'Award has not entered shortlist stage.');
            }

            // check if final stage is reached
            if (!$award->currentShortlistStage->stage->mark_as_final) {
                return back()->with('error', 'Only awards in the final stage can be approved.');
            }

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

        if (!$isAdmin) {
            $this->awardService->applySystemShortlistStages($award->type);
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
        if (! userHasPermission('awards.delete-submission')) {
            return back()->with('error', 'You do not have permission to delete award submissions.');
        }

        try {
            DB::transaction(function () use ($award) {
                $award->entry()->delete();
                $award->forceDelete();
            });

            return redirect()->back()->with('message', 'Award nomination and associated entry data deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Failed to delete Award ID [{$award->id}]: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while trying to delete the award nomination.');
        }
    }

    public function archive(Award $award)
    {
        if (! userHasPermission('awards.archive-submission')) {
            return back()->with('error', 'You do not have permission to archive award submissions.');
        }

        try {
            $award->update(['is_archive' => true]);

            return redirect()->back()->with('message', 'Award nomination archived successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to archive Award ID [{$award->id}]: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while trying to archive the award nomination.');
        }
    }

    public function restore($id)
    {
        try {
            $award = Award::findOrFail($id);
            $award->update(['is_archive' => false]);

            return redirect()->back()->with('message', 'Award nomination restored successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to restore Award ID [{$id}]: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while trying to restore the award nomination.');
        }
    }

    public function permanentDelete($id)
    {
        if (! userHasPermission('awards.delete-submission')) {
            return back()->with('error', 'You do not have permission to delete award submissions.');
        }

        try {
            $award = Award::findOrFail($id);

            if (!$award->is_archive) {
                return redirect()->back()->with('error', 'Archive this award nomination before permanently deleting it.');
            }

            DB::transaction(function () use ($award) {
                $award->entry()->delete();

                $award->forceDelete();
            });

            return redirect()->back()->with('message', 'Award nomination and all associated entries permanently deleted.');

        } catch (\Exception $e) {
            \Log::error("Failed to permanently delete Award ID [{$id}]: " . $e->getMessage());

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
        if (! userHasPermission('awards.delete-submission')) {
            return back()->with('error', 'You do not have permission to delete award submissions.');
        }

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No entries selected for deletion.');
        }

        try {
            DB::transaction(function () use ($ids) {
                $awards = Award::whereIn('id', $ids)->get();
                foreach ($awards as $award) {
                    $award->entry()->delete();
                    $award->forceDelete();
                }
            });

            return back()->with('message', 'Selected nominations and associated entry data deleted successfully.');
        } catch (\Exception $e) {
            \Log::error("Bulk deletion loop failure: " . $e->getMessage());
            return back()->with('error', 'An error occurred while deleting the selected nominations.');
        }
    }

    public function bulkArchive(Request $request)
    {
        if (! userHasPermission('awards.archive-submission')) {
            return back()->with('error', 'You do not have permission to archive award submissions.');
        }

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No entries selected for archiving.');
        }

        try {
            Award::whereIn('id', $ids)->update(['is_archive' => true]);

            return back()->with('message', 'Selected nominations archived successfully.');
        } catch (\Exception $e) {
            \Log::error("Bulk archive loop failure: " . $e->getMessage());
            return back()->with('error', 'An error occurred while archiving the selected nominations.');
        }
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No entries selected for restoration.');
        }

        try {
            Award::whereIn('id', $ids)->update(['is_archive' => false]);

            return back()->with('message', 'Selected nominations restored successfully.');
        } catch (\Exception $e) {
            \Log::error("Bulk restore loop failure: " . $e->getMessage());
            return back()->with('error', 'An error occurred while restoring the selected nominations.');
        }
    }

    public function bulkPermanentDelete(Request $request)
    {
        if (! userHasPermission('awards.delete-submission')) {
            return back()->with('error', 'You do not have permission to delete award submissions.');
        }

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No entries selected for permanent purge.');
        }

        try {
            DB::transaction(function () use ($ids) {
                $awards = Award::whereIn('id', $ids)
                    ->where('is_archive', true)
                    ->get();
                foreach ($awards as $award) {
                    $award->entry()->delete();
                    $award->forceDelete();
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

    public function shortlist(Request $request){

        app(AwardService::class)->bulkShortlist(
            $request->ids,
            $request->shortlist_stage_id,
            $request->remarks ?? null
        );

        return back()->with('message', 'Entry moved to stage successfully.');

    }

    public function awardSettings()
    {
        // Fetches the first record or creates one with defaults if none exist
        $settings = AwardSetting::firstOrCreate(
            ['id' => 1], // Attributes to search for
            [            // Default fallback values if creating for the first time
                'allow_chapter_edit'     => null,
                'allow_chapter_comment'  => null,
                'allow_chapter_approval' => null,
                'allow_zone_edit'        => null,
                'allow_zone_comment'     => null,
                'allow_zone_approval'    => null,
                'allow_field_edit'       => null,
                'allow_field_comment'    => null,
                'allow_field_approval'   => null,
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
        $awards = Award::with([
                'entry',
                'chapter',
                'approvedBy',
            ])
            ->where('type', $type)
            ->orderBy('chapter_id')
            ->get()
            ->sortBy(fn ($award) => $award->chapter?->name
                ?? $award->entry?->select_institution
                ?? 'ZZZ')
            ->values();

        if ($awards->isEmpty()) {
            return back()->with(
                'error',
                'No award records found to download.'
            );
        }

        $fileName = sprintf(
            '%s-award-nomination-report-%s.xlsx',
            $type,
            now()->format('Y-m-d-His')
        );

        $headers = [
            'Nominee Name',
            'Email Address',
            'Phone Number',
            'Chapter',
            'Chapter Status',
            'Zone Status',
            'Field Status',
            'National Status',
            'Final Approved By',
            'Final Approved On',
            'Submission Date',
        ];

        $statusLabel = fn ($status) => match ((int) $status) {
            1       => 'Approved',
            2       => 'Rejected',
            default => 'Pending',
        };

        $allRows = [];

        foreach ($awards as $award) {

            $allRows[] = [
                'Nominee Name'      => $award->name,
                'Email Address'     => $award->email,
                'Phone Number'      => $award->phone,

                'Chapter' => $award->chapter?->name
                    ?? $award->entry?->select_institution
                    ?? '—',

                'Chapter Status'    => $statusLabel($award->chapter_status),
                'Zone Status'       => $statusLabel($award->zone_status),
                'Field Status'      => $statusLabel($award->field_status),
                'National Status'   => $statusLabel($award->national_status),

                'Final Approved By' => $award->approvedBy?->name ?? '—',

                use Illuminate\Support\Carbon; // Make sure Carbon is imported at the top of your controller

                'Final Approved On' => $award->national_approved_on
                    ? Carbon::parse($award->national_approved_on)->format('d M Y, h:i A')
                    : '—',

                'Submission Date' => $award->created_at
                    ? Carbon::parse($award->created_at)->format('Y-m-d h:i A')
                    : '—',

                // Internal filtering fields
                '_chapter_raw'  => (int) $award->chapter_status,
                '_zone_raw'     => (int) $award->zone_status,
                '_field_raw'    => (int) $award->field_status,
                '_national_raw' => (int) $award->national_status,
            ];
        }

        $sheetsData = [
            'All Nominees'             => collect($allRows),
            'Passed Chapter Clearance' => collect($allRows)->where('_chapter_raw', 1),
            'Passed Zone Clearance'    => collect($allRows)->where('_zone_raw', 1),
            'Passed Field Clearance'   => collect($allRows)->where('_field_raw', 1),
            'Passed National Approval' => collect($allRows)->where('_national_raw', 1),
        ];

        foreach ($sheetsData as $sheetName => $rows) {

            $sheetsData[$sheetName] = $rows
                ->map(function ($row) {

                    unset(
                        $row['_chapter_raw'],
                        $row['_zone_raw'],
                        $row['_field_raw'],
                        $row['_national_raw']
                    );

                    return $row;
                })
                ->values()
                ->toArray();
        }

        return ExcelService::downloadMultipleSheets(
            $sheetsData,
            $headers,
            $fileName
        );
    }

    public function awardAssetsDownload()
    {
        if (! Award::exists()) {
            return back()->with(
                'error',
                'No award records found to download.'
            );
        }

        $awardFields = awardFormFields();

        $imageFields = collect($awardFields)
            ->filter(fn ($field) => $field['type'] === 'image')
            ->keys()
            ->toArray();

        $documentFields = collect($awardFields)
            ->filter(fn ($field) => $field['type'] === 'file')
            ->keys()
            ->toArray();

        $zipFileName = now()->format('Y-m-d-His')
            . '-award-assets.zip';

        $zipDirectory = base_path(
            'protected_uploads/award-assets'
        );

        if (! file_exists($zipDirectory)) {
            mkdir($zipDirectory, 0777, true);
        }

        $zipFilePath = "{$zipDirectory}/{$zipFileName}";

        $zip = new ZipArchive();

        if (
            $zip->open(
                $zipFilePath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            ) !== true
        ) {
            return back()->with(
                'error',
                'Could not create ZIP archive.'
            );
        }

        $zip->addEmptyDir('images');
        $zip->addEmptyDir('documents');

        $hasAddedFiles = false;

        @set_time_limit(300);
        ini_set('memory_limit', '512M');

        Award::with('entry')
            // ->where('national_status', 0)
            ->latest()
            ->chunk(20, function ($awards) use (
                $zip,
                $imageFields,
                $documentFields,
                &$hasAddedFiles
            ) {

                foreach ($awards as $award) {

                    if (! $award->entry) {
                        continue;
                    }

                    $nomineeSlug = str($award->name ?? 'Unnamed')
                        ->replace([' ', '/', '\\'], '_');

                    foreach (
                        array_merge($imageFields, $documentFields)
                        as $field
                    ) {

                        $value = $award->entry->{$field};

                        if (
                            blank($value)
                            || str_starts_with(
                                $value,
                                'Download Failed:'
                            )
                        ) {
                            continue;
                        }

                        try {

                            $decodedPath = base64_decode(
                                ltrim($value, '/')
                            );

                            $fullPath = base_path(
                                'protected_uploads/'
                                . ltrim($decodedPath, '/')
                            );

                            if (! file_exists($fullPath)) {
                                Log::warning(
                                    "Missing file: {$fullPath}"
                                );

                                continue;
                            }

                            $extension = pathinfo(
                                $fullPath,
                                PATHINFO_EXTENSION
                            );

                            $fileName = sprintf(
                                '%s_%s.%s',
                                $nomineeSlug,
                                $field,
                                $extension
                            );

                            $folder = in_array(
                                $field,
                                $imageFields
                            )
                                ? 'images/'
                                : 'documents/';

                            $zip->addFile(
                                $fullPath,
                                $folder . $fileName
                            );

                            $hasAddedFiles = true;

                        } catch (\Throwable $e) {

                            Log::error(
                                "Failed adding asset for Award ID [{$award->id}], Field [{$field}]: {$e->getMessage()}"
                            );
                        }
                    }
                }

                unset($awards);

                gc_collect_cycles();
            });

        $zip->close();

        if (
            $hasAddedFiles
            && file_exists($zipFilePath)
        ) {
            return response()
                ->download($zipFilePath, $zipFileName)
                ->deleteFileAfterSend(true);
        }

        if (file_exists($zipFilePath)) {
            @unlink($zipFilePath);
        }

        return back()->with(
            'error',
            'No binary attachments were found to include in the ZIP package.'
        );
    }

    public function adjustAwardStatus(
        Request $request,
        Award $award
    ) {
        if (! userHasPermission('awards.adjust-approval-status')) {
            return back()->with('error', 'You do not have permission to adjust award approval statuses.');
        }

        $status = $request->approval_status;

        $updates = match ($status) {
            'chapter_pending' => [
                'chapter_status' => 0,
            ],

            'chapter_approved' => [
                'chapter_status' => 1,
                'chapter_comment' => null,
            ],

            'chapter_rejected' => [
                'chapter_status' => 2,
                'chapter_comment' => $request->rejection_reason,
            ],

            'zone_pending' => [
                'zone_status' => 0,
            ],

            'zone_approved' => [
                'zone_status' => 1,
                'zone_comment' => null,
            ],

            'zone_rejected' => [
                'zone_status' => 2,
                'zone_comment' => $request->rejection_reason,
            ],

            'field_pending' => [
                'field_status' => 0,
            ],

            'field_approved' => [
                'field_status' => 1,
                'field_comment' => null,
            ],

            'field_rejected' => [
                'field_status' => 2,
                'field_comment' => $request->rejection_reason,
            ],

            'national_pending' => [
                'national_status' => 0,
                'national_rejected_on' => now(),
                'national_rejected_by' => auth()->user()->id
            ],

            'national_approved' => [
                'national_status' => 1,
                'national_comment' => null,
                'national_approved_on' => now(),
                'national_approved_by' => auth()->user()->id

            ],

            'national_rejected' => [
                'national_status' => 2,
                'national_comment' => $request->rejection_reason,
                'national_rejected_on' => now(),
                'national_rejected_by' => auth()->user()->id
            ],

            default => throw new \InvalidArgumentException(
                'Invalid approval status supplied.'
            ),
        };

        $award->update($updates);

        $this->awardService->applySystemShortlistStages($award->type);

        return back()->with(
            'success',
            'Report status updated successfully.'
        );
    }

    public function firstClassSubmission()
    {
        $type = 'go';
        $settings = AwardSetting::first();

        // Check if deadline exists and if it has passed
        if ($settings && $settings->first_class_awards_deadline && Carbon::now()->greaterThan($settings->first_class_awards_deadline)) {
            return view('frontend.' . frontendTemplate() . '.award-closed', compact('type'));
        }

        $fields = collect(awardFormFields())
            ->filter(fn ($field) => in_array($field['award_type'] ?? 'both', ['both', $type]));

        return view('frontend.' . frontendTemplate() . '.award-submission', compact('fields', 'type'));
    }

    public function etfEntrySubmission()
    {
        $type = 'etf';
        $settings = AwardSetting::first();

        if ($settings && $settings->etf_awards_deadline && Carbon::now()->greaterThan($settings->etf_awards_deadline)) {
            return view('frontend.' . frontendTemplate() . '.award-closed', compact('type'));
        }

        $fields = collect(awardFormFields())
            ->filter(fn ($field) => in_array($field['award_type'] ?? 'both', ['both', $type]));

        return view('frontend.' . frontendTemplate() . '.award-submission', compact('fields', 'type'));
    }

    public function submitAwardEntry(Request $request)
    {
        $type = $request->type;

        $settings = AwardSetting::first();

        $deadlineColumn = $type === 'go'
            ? 'first_class_awards_deadline'
            : 'etf_awards_deadline';

        if (
            $settings &&
            filled($settings->{$deadlineColumn}) &&
            now()->greaterThan($settings->{$deadlineColumn})
        ) {
            return response()->view(
                'frontend.' . frontendTemplate() . '.award-closed',
                compact('type'),
                403
            );
        }

        $data = $request->input('entries', []);

        $existing = Award::where('type', $type)
            ->whereHas('entry', function ($query) use ($data) {
                $query->whereRaw(
                    'LOWER(email_address) = ?',
                    [strtolower($data['email_address'])]
                );
            })
            ->exists();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'You have already submitted an application for this award.');
        }

        try {

            DB::beginTransaction();

            foreach (['result_file', 'picture'] as $fileField) {
                if (! $request->hasFile("entries.{$fileField}")) {
                    continue;
                }

                $uploadedFile = $request->file("entries.{$fileField}");

                if (! $uploadedFile->isValid()) {
                    continue;
                }

                $data[$fileField] = app(FileUploadService::class)->secureUpload(
                    $uploadedFile,
                    'award-files'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Resolve chapter hierarchy
            |--------------------------------------------------------------------------
            */
            $chapterId = $data['chapter_id'] ?? null;

            $chapter = $chapterId
                ? Chapter::find($chapterId)
                : null;

            $award = Award::create([
                'type'            => $type,
                'chapter_id'      => $chapter->id,
                'zone_id'         => $chapter->zone->id,
                'field_id'        => $chapter->field->id,
                'reference'       => strtoupper($type . '-' . uniqid()),
                'zone_status'     => 0,
                'field_status'    => 0,
                'national_status' => 0,
            ]);

            $award->entry()->create($data);

            DB::commit();

            return redirect()
                ->back()
                ->with(
                    'message',
                    'Your application has been submitted successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'An error occurred while submitting your application. Please try again.'
                );
        }
    }
}
