<?php

namespace App\Http\Controllers;

use App\Enums\EmailTypeEnum;
use App\Models\Award;
use App\Models\AwardEntries;
use App\Models\AwardSetting;
use App\Models\Chapter;
use App\Services\AwardService;
use App\Services\EmailService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        $request->merge([
            'type' => 'go'
        ]);

        $entries = $this->awardService->index($request, $user, 'go', $isAdmin);
        $title = 'General Overseer (G.O.) Award Submissions';
        return view('admin.awards.index', array_merge(compact('isAdmin','entries', 'user', 'title')));
    }

    public function etfAwardEntries(Request $request){
        $adminDetails = isAdmin();
        $isAdmin = $adminDetails['status'];
        $user = $adminDetails['user'];

        $request->merge([
            'type' => 'etf'
        ]);

        $entries = $this->awardService->index($request, $user, 'etf', $isAdmin);
        $title = 'EducationTrust Fund (E.T.F.) Award Submissions';
        
        return view('admin.awards.index', array_merge(compact('isAdmin','entries', 'user', 'title')));
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
        
        return view('admin.awards.show', compact('isAdmin', 'isAdmin', 'award', 'chapters', 'user'));
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
        if (!$permissions->canEdit) {
            return back()->with('error', 'Invalid Action: You do not have permission to modify this form.');
        }

        // 3. Establish lookup assets
        $fileFields = fileFields();
        $entries = $request->entries;
        // dd($entries, $request->all());
        try {
            $adminUpdates = $request->only(["chapter_comment", "zone_comment", "field_comment", "national_comment"]);
            
            if(isAdmin()['status']){
                $award->update($adminUpdates);
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

            return back()->with('success', 'Award entries updated successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'An error occurred while saving records: ' . $e->getMessage());
        }
    }

    public function approveEntry(Request $request, Award $award)
    {
        $user = auth()->user() ?? auth()->guard('stakeholder')->user();
        $userRole = (int)($user->role_id ?? $user->role ?? 0);
        $comment = $request->input('comment'); // Kept generic 'comment' for approval notes
        
        $isAdmin = isAdmin()['status'];

        $updateData = [];

        if (in_array($userRole, chapterStakeholders())) {
            $updateData['chapter_status'] = 1;
            $updateData['chapter_comment'] = $comment;
        }

        if (in_array($userRole, zoneStakeholders())) {
            $updateData['zone_status'] = 1; 
            $updateData['zone_comment'] = $comment;
        }

        if (in_array($userRole, fieldStakeholders())) {
            $updateData['field_status'] = 1; 
            $updateData['field_comment'] = $comment;
        }

        if ($isAdmin) {
            $updateData['national_status'] = 1;
            $updateData['national_comment'] = $comment;
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
            
            // Execute logging and database queuing
            $log = EmailService::logEmail($emailsToQueue);
        }

        return back()->with('message', 'Operation Successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Award $award)
    {
        //
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

        $settings->update($request->only([
            'allow_chapter_edit', 'allow_chapter_comment', 'allow_chapter_approval',
            'allow_zone_edit',    'allow_zone_comment',    'allow_zone_approval',
            'allow_field_edit',   'allow_field_comment',   'allow_field_approval'
        ]));

        return redirect()->back()->with('message', 'System configurations saved successfully.');
    }

 
    public function rejectEntry(Request $request, Award $award)
    {
        $user = auth()->user() ?? auth()->guard('stakeholder')->user();
        $userRole = (int)($user->role_id ?? $user->role ?? 0);
        $comment = $request->input('rejection_reason');

        $isAdmin = isAdmin()['status'];

        // Dynamic array to track what needs to be updated in the database
        $updateData = [];

        if (in_array($userRole, chapterStakeholders())) {
            $updateData['chapter_status'] = 2; 
            $updateData['chapter_comment'] = $comment;
        }

        if (in_array($userRole, zoneStakeholders())) {
            $updateData['zone_status'] = 2; 
            $updateData['zone_comment'] = $comment;
        }

        if (in_array($userRole, fieldStakeholders())) {
            $updateData['field_status'] = 2; // Note: Fixed 'chapter_status' to 'field_status' here
            $updateData['field_comment'] = $comment;
        }

        if ($isAdmin) {
            $updateData['national_status'] = 2;
            $updateData['national_comment'] = $request->input('comment');
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
            
            // 4. Execute logging and debug
            $log = EmailService::logEmail($emailsToQueue);
        }

        return back()->with('message', 'Operation Successful');
    }
}
