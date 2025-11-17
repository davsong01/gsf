<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Reports;
use App\Models\Setting;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use App\Models\StakeholderReport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\StakeholderReportAnswer;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;
use App\Services\ReportNotificationService;

class StakeholderReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::guard('stakeholder')->user();
        $role = $user->role;
        
        // Base queries
        $chapters = Chapter::query();
        $zones = Zone::query();
        $fields = Field::query();

        // Scope variables for filtering reports
        $chapterIds = collect();
        $zoneIds = collect();
        $fieldIds = collect();

        /** =====================
         * ROLE-BASED SCOPING
         * ===================== */
        if (in_array($role, ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary'])) {
            $chapterIds = collect([$user->chapter_id]);
            $zoneIds = collect([$user->zone_id]);
            $fieldIds = collect([$user->field_id]);
        } elseif ($role === 'Zonal Pastor') {
            $zoneIds = collect([$user->zone_id]);

            // All chapters under this zone
            $chapterIds = Chapter::where('zone_id', $user->zone_id)->pluck('id');

            // Fields that contain this zone
            $fieldIds = Field::whereHas('zones', fn($q) => $q->where('id', $user->zone_id))
                ->pluck('id');
        } elseif ($role === 'Field Pastor') {
            $fieldIds = collect([$user->field_id]);

            // Zones under this field
            $zoneIds = Zone::where('field_id', $user->field_id)->pluck('id');

            // Chapters under all zones in this field
            $chapterIds = Chapter::whereIn('zone_id', $zoneIds)->pluck('id');
        }

        // Secretariat → full access (no scoping)
        elseif ($role === 'Secretariat') {
            $chapterIds = Chapter::pluck('id');
            $zoneIds = Zone::pluck('id');
            $fieldIds = Field::pluck('id');
        }

        /** =====================
         * FILTER REPORTS BY SCOPED IDS
         * ===================== */
        $reports = StakeholderReport::query()
            ->when($chapterIds->isNotEmpty(), fn($q) => $q->whereIn('chapter_id', $chapterIds))
            ->when($zoneIds->isNotEmpty(), fn($q) => $q->whereIn('zone_id', $zoneIds))
            ->when($fieldIds->isNotEmpty(), fn($q) => $q->whereIn('field_id', $fieldIds));

        /** =====================
         * DATE FILTERS
         * ===================== */
        if ($request->filled('from_date')) {
            $reports->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $reports->whereDate('created_at', '<=', $request->to_date);
        }

        /** =====================
         * MANUAL FILTERS
         * ===================== */
        if ($request->filled('chapter_filter')) {
            $reports->where('chapter_id', $request->chapter_filter);
        }

        if ($request->filled('zone_filter')) {
            $reports->where('zone_id', $request->zone_filter);
        }

        if ($request->filled('field_filter')) {
            $reports->where('field_id', $request->field_filter);
        }

        /** =====================
         * STATUS FILTERS
         * ===================== */
        if ($request->filled('status_filter')) {
            $statusMap = [
                'field_pending' => ['field_status', 0],
                'field_approved' => ['field_status', 1],
                'field_rejected' => ['field_status', 2],
                'zone_pending' => ['zone_status', 0],
                'zone_approved' => ['zone_status', 1],
                'zone_rejected' => ['zone_status', 2],
                'national_pending' => ['national_status', 0],
                'national_approved' => ['national_status', 1],
                'national_rejected' => ['national_status', 2],
            ];

            if (isset($statusMap[$request->status_filter])) {
                [$column, $value] = $statusMap[$request->status_filter];
                $reports->where($column, $value);
            }
        }

        /** =====================
         * EXECUTE QUERIES
         * ===================== */
        $reports = $reports->with(['chapter', 'zone', 'field'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $chapters = $chapters->whereIn('id', $chapterIds)->orderBy('name')->get();
        $zones = $zones->whereIn('id', $zoneIds)->orderBy('name')->get();
        $fields = $fields->whereIn('id', $fieldIds)->orderBy('name')->get();

        /** =====================
         * REDIRECT FOR FINANCIAL SECRETARY
         * ===================== */
        if ($role === 'Financial Secretary') {
            return redirect(route('stakeholderpayment.index'));
        }
        return view('stakeholder.index', compact('reports', 'chapters', 'fields', 'zones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $months = $this->getMonths();
        $user = Auth::guard('stakeholder')->user();
        $chapter = $user->chapter;

        $sections = StakeholderQuestionSection::isActive()->with([
            'subsections.questions' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('id')->get();

        $prefillData = [
            'chapter_name' => $chapter->name ?? '',
            'month' => date('m'),
            'year' => date('Y'),
            'year_established' => $chapter->year_established ?? '',
            'session' => date('Y') - 1 . '/'. date('Y'),
            'president_name' => optional($chapter->stakeholders->where('role', 'Chapter President')->first())->name ?? '',
        ];
        
        return view('stakeholder.create', compact('months', 'sections', 'prefillData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $stakeholder = Auth::guard('stakeholder')->user();

        $checks = $this->checks($stakeholder);
        
        // Validate the form data
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'nullable',
            'confirm_information' => 'accepted',
        ]);

        $chapter = Chapter::with('zone:id','field:id')->where('id', $stakeholder->chapter_id)->first();

        if (empty($chapter->year_established)) {
            $chapter->update(['year_established' => $validated['responses']['year_established']]);
        }

        DB::beginTransaction();

        try {
            // Build report meta data
            $reportData = [
                'chapter_id' => $stakeholder->chapter_id,
                'zone_id' => $stakeholder->zone_id ?? $chapter?->zone->id,
                'field_id' => $stakeholder->field_id ?? $chapter?->field->id,
                'stakeholder_id' => $stakeholder->id,
                'session' => $validated['responses']['session'] ?? null,
                'year' => $validated['responses']['year'] ?? null,
                'month' => $validated['responses']['month'] ?? null,

            ];

            // Create the main report record
            $report = StakeholderReport::create($reportData);
            
            // Save each response to StakeholderReportAnswer
            foreach ($validated['responses'] as $slug => $answer) {
                $question = StakeholderReportQuestion::where('slug', $slug)->first();
                if ($question) {
                    StakeholderReportAnswer::create([
                        'report_id' => $report->id,
                        'question_id' => $question->id,
                        'answer_value' => is_array($answer) ? json_encode($answer) : $answer,
                    ]);
                }
            }

            ReportNotificationService::handleReportSubmissionSubmission($report, $stakeholder, 'submit');
            
            DB::commit();

            return redirect(route('stakeholders.reports.index'))->with('message', 'Report saved successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'An error occurred while saving the report. ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, StakeholderReport $report)
    {
        $stakeholder = Auth::guard('stakeholder')->user();

        $checks = $this->checks($stakeholder);
        
        // Validate the form data
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'nullable',
            'confirm_information' => 'accepted',
        ]);

        $chapter = Chapter::with('zone:id', 'field:id')->where('id', $stakeholder->chapter_id)->first();

        if(empty($chapter->year_established)){
            $chapter->update(['year_established' => $validated['responses']['year_established']]);
        }

        DB::beginTransaction();

        try {
            $report->update([
                'chapter_id' => $stakeholder->chapter_id,
                'zone_id' => $stakeholder->zone_id ?? $chapter?->zone->id,
                'field_id' => $stakeholder->field_id ?? $chapter?->field->id,
                'stakeholder_id' => $stakeholder->id,
                'session' => $validated['responses']['session'] ?? $report->session,
                'year' => $validated['responses']['year'] ?? $report->year,
                'month' => $validated['responses']['month'] ?? $report->month,
            ]);

            // Loop through each response and update or create answers
            foreach ($validated['responses'] as $slug => $answer) {
                $question = StakeholderReportQuestion::where('slug', $slug)->first();
                if (!$question) continue;

                $answerValue = is_array($answer) ? json_encode($answer) : $answer;

                // Update existing answer or create new
                StakeholderReportAnswer::updateOrCreate(
                    [
                        'report_id' => $report->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'answer_value' => $answerValue,
                    ]
                );
            }

            ReportNotificationService::handleReportSubmissionSubmission($report, $stakeholder, 'update');

            DB::commit();

            return redirect(route('stakeholders.reports.index'))->with('message', 'Report updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            // dd($e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while updating the report. ' . $e->getMessage()]);
        }
    }


    public function checks($stakeholder){

        $data = [
            'status' => true,
            'message' => 'success'
        ];

        if (
            is_null($stakeholder->signature) ||
            is_null($stakeholder->gen_sec_signature) ||
            is_null($stakeholder->fin_sec_signature) ||
            is_null($stakeholder->evang_sec_signature)
        ) {
            $data = [
                'status' => false,
                'message' => 'Kindly upload signatures first, you will only need to do this once'
            ];
        }

        return $data;
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    public function show(StakeholderReport $report)
    {
        $sections = StakeholderQuestionSection::isActive()->with([
            'subsections.questions' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('id')->get();

        return view('stakeholder.show', compact('report','sections'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    public function edit(StakeholderReport $report)
    {
        $report->load('answers'); // eager load answers
        $months = $this->getMonths();
        $user = Auth::guard('stakeholder')->user();
        $chapter = $user->chapter;

        $sections = StakeholderQuestionSection::isActive()->with([
            'subsections.questions' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('id')->get();

        // Only used for static/default fields in the form (chapter info, month/year/session)
        $prefillData = [
            'chapter_name' => $chapter->name ?? '',
            'year_established' => $chapter->year_established ?? '',
            'president_name' => optional($chapter->stakeholders->where('role', 'Chapter President')->first())->name ?? '',
        ];

        // Prepare answers array keyed by question_slug for edit mode
        $answersData = [];
        if ($report->answers) {
            foreach ($report->answers as $answer) {
                $decoded = json_decode($answer->answer, true);
                $answersData[$answer->question_slug] = $decoded ?? $answer->answer;
            }
        }
        
        return view('stakeholder.create', compact('months', 'report', 'sections', 'prefillData', 'answersData'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Reports $report)
    // {
    //     if(Auth::guard('stakeholder')->user()->role == 'President'){
    //         $data = $this->validateRequestData($request);
        
    //         if(is_null(Auth::guard('stakeholder')->user()->signature) || is_null(Auth::guard('stakeholder')->user()->gen_sec_signature) || is_null(Auth::guard('stakeholder')->user()->fin_sec_signature) || is_null(Auth::guard('stakeholder')->user()->evang_sec_signature)){
    //             return back()->with('message', 'Kindly Upload signatures first, you will only need to do this once');
    //         }
    
    //         if(!is_null(Auth::guard('stakeholder')->user()->chapter_id)){
    //             $data['chapter_id'] = Auth::guard('stakeholder')->user()->chapter_id;
    //         }
            
    //         $data['zone_reject_comment'] = null;
    //         $data['field_reject_comment' ] = null;
    //         $data['status_complete_reject_comment' ] = null;

    //         $report->update($data);
            
    //         //Send Email  
    //         if($report->zone->stakeholder){
    //             $data = [
    //                 'type' => 'resend',
    //                 'addressee' => $report->zone->stakeholder->name,
    //                 'chapter' => $report->chapter->name,
    //                 'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year,
    //             ];
    
    //             Mail::to($report->zone->stakeholder->email)->send(new NotificationEmail($data));
    //         }
        
    //     }

    //     if(Auth::guard('stakeholder')->user()->role == 'Zonal Pastor'){
    //         $report->zonal_pastor_affirmation = Auth::guard('stakeholder')->user()->name;
    //         $report->zone_status = 1;
    //     }

    //     if(Auth::guard('stakeholder')->user()->role == 'Field Pastor'){
    //         $report->field_pastor_approval = Auth::guard('stakeholder')->user()->name;
    //         $report->field_status = 1;
    //         $report->zone_status = 1;
    //     }

    //     if(Auth::guard('stakeholder')->user()->role == 'Secretariat'){
    //         $report->field_pastor_approval = Auth::guard('stakeholder')->user()->name;
    //         $report->ncp_comment = $request->ncp_comment;
    //         $report->field_status = 1;
    //         $report->zone_status = 1;
    //         $report->status_complete = 1;
    //     }

    //     $report->save();

    //     $report->update($this->validateRequestData($request));
        
    //     //Send mail notification
    //     if(Auth::guard('stakeholder')->user()->role == 'Zonal Pastor'){
    //         //Get field pastor
    //         $zonalPastor = $report->zone->stakeholder;

    //         //send mail to Zonal Pastor
    //         if($zonalPastor){
    //             $data = [
    //                 'type' => 'zone',
    //                 'addressee' => $zonalPastor->name,
    //                 'chapter' => $report->chapter->name,
    //                 'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year
    //             ];

    //             Mail::to($zonalPastor->email)->send(new NotificationEmail($data));
    //         }

    //         //
    //     }
        
    //     if(Auth::guard('stakeholder')->user()->role == 'Field Pastor'){
    //         //send mail to Secretary
    //         $secretary = Stakeholder::whereRole('Secretariat')->wherePortfolio('Gen Sec')->first();
    //         if($secretary){
    //             $data = [
    //                 'type' => 'zone',
    //                 'addressee' => $secretary->name,
    //                 'chapter' => $report->chapter->name,
    //                 'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year
    //             ];

    //             Mail::to($secretary->email)->send(new NotificationEmail($data));
    //         }
            
    //     }

    //     return redirect(route('stakeholders.dashboard'))->with('message', 'operation successful!');
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    public function rejectReport(Request $request){
        // dd(Auth::guard('stakeholder')->user()->role);
        $report =  StakeholderReport::whereId($request->report_id)->first();
        if(Auth::guard('stakeholder')->user()->role == 'Zonal Pastor'){
            $type = 'zonalRejection';
            $report->zone_reject_comment = $request->comment;
            $report->zone_status = 0;
            $report->save();
        }
        if(Auth::guard('stakeholder')->user()->role == 'Field Pastor'){
            $type = 'fieldRejection';
            $report->field_reject_comment = $request->comment;
            $report->field_status = 0;
            $report->zone_status = 0;
            $report->save();
        }

        if(Auth::guard('stakeholder')->user()->role == 'Secretariat'){
            $type = 'nationalRejection';
            $report->status_complete_reject_comment = $request->comment;
            $report->status_complete = 0;
            $report->field_status = 0;
            $report->save();
        }
       
        //Email President
        $president = $report->chapter->stakeholder;
            if($president){
                $data = [
                    'type' => $type,
                    'comment' => $request->comment,
                    'addressee' => $president->name,
                    'chapter' => $report->chapter->name,
                    'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year
                ];

                Mail::to($president->email)->send(new NotificationEmail($data));
            }
        

        return redirect(route('stakeholders.dashboard'))->with('message', 'operation successful!');
    }
    
    public function destroy(Reports $reports)
    {
        //
    }

    public function delete($id){
        if(Auth::guard('stakeholder')->user()->role != 'Secretariat') return abort(404);
        $report =  StakeholderReport::find($id);
        if($report->stakeholderpayment){
            if (file_exists(base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image ))
                unlink( base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image );

                $report->stakeholderpayment->delete();
        }
        $report->delete();
       
        return back()->with('message', 'Report has been deleted forever!');
    }

   

    // private function validateRequestData($request){
    //     $rules = [];

    //     foreach ($sections as $section) {
    //         foreach ($section->subsections as $subsection) {
    //             foreach ($subsection->questions as $question) {
    //                 $field = 'responses.' . $question->slug;

    //                 // Build rules dynamically
    //                 $rules[$field] = $question->is_required ? 'required' : 'nullable';

    //                 // Add type-based validation if quantifiable or specific
    //                 if ($question->type === 'number' || $question->is_quantifiable) {
    //                     $rules[$field] .= '|numeric';
    //                 } elseif ($question->type === 'date') {
    //                     $rules[$field] .= '|date';
    //                 } elseif ($question->type === 'email') {
    //                     $rules[$field] .= '|email';
    //                 }
    //             }
    //         }
    //     }

    //     // Now validate using dynamic rules
    //     $data = $this->validate($request, $rules);

    //     return $data;
    // }
}
