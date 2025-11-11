<?php

namespace App\Http\Controllers;

use App\Models\Reports;
use App\Models\Setting;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;

class StakeholderReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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

        $sections = StakeholderQuestionSection::with([
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
        // $validated = $request->validate([
        //     'responses' => 'required|array',
        //     'responses.*' => 'nullable',
        //     'confirm_information' => 'accepted',
        // ]);

        DB::beginTransaction();

        try {
            // Build report meta data
            $reportData = [
                'chapter_id' => $stakeholder->chapter_id,
                'zone_id' => $stakeholder->zone_id,
                'field_id' => $stakeholder->field_id,
                'year' => date('Y'),
                'month' => date('n'),
            ];
            dd($reportData, $stakeholder);
            // Create the main report record
            $report = StakeholderReport::create($reportData);

            // Save each response to StakeholderReportAnswer
            foreach ($validated['questions'] as $slug => $answer) {
                $question = StakeholderReportQuestion::where('slug', $slug)->first();
                if ($question) {
                    StakeholderReportAnswer::create([
                        'report_id' => $report->id,
                        'question_id' => $question->id,
                        'answer_value' => is_array($answer) ? json_encode($answer) : $answer,
                    ]);
                }
            }

            // Send notification email
            if ($report->zone && $report->zone->stakeholder) {
                $mailData = [
                    'type' => 'zone',
                    'addressee' => $report->zone->stakeholder->name,
                    'chapter' => $report->chapter->name,
                    'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year,
                ];

                Mail::to($report->zone->stakeholder->email)->send(new NotificationEmail($mailData));
            }

            DB::commit();

            return redirect(route('stakeholder.dashboard'))->with('message', 'Report saved successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while saving the report. ' . $e->getMessage()]);
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
    public function show(Reports $report)
    {
        return view('stakeholder.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    public function edit(Reports $report)
    {
        if($report->zone_status == 1){
            return abort(404);
        }        
        $months = $this->getMonths();
        

        if(Auth::guard('stakeholder')->user()->role == 'Zonal Pastor' || Auth::guard('stakeholder')->user()->role == 'Field Pastor'){
            $editStatus = 'readonly';
        }

        if(Auth::guard('stakeholder')->user()->role == 'President'){

            $editStatus = '';
        }

        if(Auth::guard('stakeholder')->user()->role == 'Secretariat'){

            $editStatus = 'readonly';
        }


        return view('stakeholder.update_report', compact('months', 'report', 'editStatus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reports  $reports
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reports $report)
    {
        if(Auth::guard('stakeholder')->user()->role == 'President'){
            $data = $this->validateRequestData($request);
        
            if(is_null(Auth::guard('stakeholder')->user()->signature) || is_null(Auth::guard('stakeholder')->user()->gen_sec_signature) || is_null(Auth::guard('stakeholder')->user()->fin_sec_signature) || is_null(Auth::guard('stakeholder')->user()->evang_sec_signature)){
                return back()->with('message', 'Kindly Upload signatures first, you will only need to do this once');
            }
    
            if(!is_null(Auth::guard('stakeholder')->user()->chapter_id)){
                $data['chapter_id'] = Auth::guard('stakeholder')->user()->chapter_id;
            }
            
           
            $data['zone_reject_comment'] = null;
            $data['field_reject_comment' ] = null;
            $data['status_complete_reject_comment' ] = null;

            $report->update($data);
            
            //Send Email  
            if($report->zone->stakeholder){
                $data = [
                    'type' => 'resend',
                    'addressee' => $report->zone->stakeholder->name,
                    'chapter' => $report->chapter->name,
                    'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year,
                ];
    
                Mail::to($report->zone->stakeholder->email)->send(new NotificationEmail($data));
            }
        
        }

        if(Auth::guard('stakeholder')->user()->role == 'Zonal Pastor'){
            $report->zonal_pastor_affirmation = Auth::guard('stakeholder')->user()->name;
            $report->zone_status = 1;
        }

        if(Auth::guard('stakeholder')->user()->role == 'Field Pastor'){
            $report->field_pastor_approval = Auth::guard('stakeholder')->user()->name;
            $report->field_status = 1;
            $report->zone_status = 1;
        }

        if(Auth::guard('stakeholder')->user()->role == 'Secretariat'){
            $report->field_pastor_approval = Auth::guard('stakeholder')->user()->name;
            $report->ncp_comment = $request->ncp_comment;
            $report->field_status = 1;
            $report->zone_status = 1;
            $report->status_complete = 1;
        }

        $report->save();

        $report->update($this->validateRequestData($request));
        
        //Send mail notification
        if(Auth::guard('stakeholder')->user()->role == 'Zonal Pastor'){
            //Get field pastor
            $zonalPastor = $report->zone->stakeholder;

            //send mail to Zonal Pastor
            if($zonalPastor){
                $data = [
                    'type' => 'zone',
                    'addressee' => $zonalPastor->name,
                    'chapter' => $report->chapter->name,
                    'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year
                ];

                Mail::to($zonalPastor->email)->send(new NotificationEmail($data));
            }

            //
        }
        
        if(Auth::guard('stakeholder')->user()->role == 'Field Pastor'){
            //send mail to Secretary
            $secretary = Stakeholder::whereRole('Secretariat')->wherePortfolio('Gen Sec')->first();
            if($secretary){
                $data = [
                    'type' => 'zone',
                    'addressee' => $secretary->name,
                    'chapter' => $report->chapter->name,
                    'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year
                ];

                Mail::to($secretary->email)->send(new NotificationEmail($data));
            }
            
        }

        return redirect(route('stakeholder.dashboard'))->with('message', 'operation successful!');
    }

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
        

        return redirect(route('stakeholder.dashboard'))->with('message', 'operation successful!');
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
