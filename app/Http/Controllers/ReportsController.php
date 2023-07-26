<?php

namespace App\Http\Controllers;

use App\Models\Reports;
use App\Models\Setting;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReportsController extends Controller
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

        return view('stakeholder.create', compact('months'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validateRequestData($request);
        
        if(is_null(Auth::guard('stakeholder')->user()->signature) || is_null(Auth::guard('stakeholder')->user()->gen_sec_signature) || is_null(Auth::guard('stakeholder')->user()->fin_sec_signature) || is_null(Auth::guard('stakeholder')->user()->evang_sec_signature)){
            return back()->with('message', 'Kindly Upload signatures first, you will only need to do this once');
        }

        if(!is_null(Auth::guard('stakeholder')->user()->chapter_id)){
            $data['chapter_id'] = Auth::guard('stakeholder')->user()->chapter_id;
        }
        
        $data['zone_id'] = Auth::guard('stakeholder')->user()->zone_id;       
        $data['year'] = date('Y');       
        $data['field_id'] = Auth::guard('stakeholder')->user()->field_id;       

        $report = Reports::create($data);
        
        //Send Email  
        if($report->zone->stakeholder){
            $data = [
                'type' => 'zone',
                'addressee' => $report->zone->stakeholder->name,
                'chapter' => $report->chapter->name,
                'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year,
            ];

            Mail::to($report->zone->stakeholder->email)->send(new NotificationEmail($data));
        }
    

		return redirect(route('stakeholder.dashboard'))->with('message', 'Report saved successfully');
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
        $report = Reports::whereId($request->report_id)->first();
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
        $report = Reports::find($id);
        if($report->stakeholderpayment){
            if (file_exists(base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image ))
                unlink( base_path() . '/uploads/paymentproof' . '/' . $report->stakeholderpayment->image );

                $report->stakeholderpayment->delete();
        }
        $report->delete();
       
        return back()->with('message', 'Report has been deleted forever!');
    }

   

    private function validateRequestData($request){
        $data = $this->validate($request, [
            'session' => 'required',
            'semester' => 'required|numeric',
            'day' => 'required|numeric',
            'month' => 'required',
            'president_name' => 'required',
            'president_number' => 'required',
            'gen_sec_name' => 'nullable',
            'gen_sec_number' => 'nullable',
            'evang_sec_name' => 'nullable',
            'evang_sec_number' => 'nullable',
            'fin_sec_name' => 'nullable',
            'fin_sec_number' => 'nullable',
            'bible_study_venue' => 'nullable',
            'bible_study_time' => 'nullable',
            'bible_study_highest_attendance' => 'nullable',
            'bible_study_lowest_attendance' => 'nullable',
            'prayer_meeting_venue' => 'nullable',
            'prayer_meeting_time' => 'nullable',
            'prayer_meeting_highest_attendance' => 'nullable',
            'prayer_meeting_lowest_attendance' => 'nullable',
            'believer_foundation_class_venue' => 'nullable',
            'believer_foundation_class_time' => 'nullable',
            'believer_foundation_class_highest_attendance' => 'nullable',
            'believer_foundation_class_lowest_attendance' => 'nullable',
            'sunday_school_highest_attendance' => 'nullable',
            'sunday_school_lowest_attendance' => 'nullable',
            'sunday_highest_attendance' => 'nullable',
            'sunday_lowest_attendance' => 'nullable',
            'visit_to_assembly_venue' => 'nullable',
            'visit_to_assembly_time' => 'nullable',
            'visit_to_assembly_fellowship_attendance' => 'nullable',
            'visit_to_assembly_fellowship_activity' => 'nullable',
            'special_programs' => 'nullable',
            'evangelism_report' => 'nullable',
            'evangelism_number_of_souls' =>  'nullable',
            'evangelism_number_of_souls_who_joined_fellowship' =>  'nullable',
            'evangelism_follow_up_efforts' =>  'nullable',
            'evangelism_number_of_converts_baptized' =>  'nullable',
            'bible_study_offering' =>  'nullable',
            'prayer_meeting_offering' =>  'nullable',
            'special_program_offering' =>  'nullable',
            'other_special_program_offering' =>  'nullable',
            'thanksgiving_offering' =>  'nullable',
            'total_sunday_worship_offering' =>  'nullable',
            'grand_total_offering' =>  'nullable',
            'president_tithe' =>  'nullable',
            'total_executive_tithe' =>  'nullable',
            'total_workers_tithe' =>  'nullable',
            'total_members_tithe' =>  'nullable',
            'grand_total_tithe' =>  'nullable',
            'tithe_of_tithe' =>  'nullable',
            'other_levies_purpose' =>  'nullable',
            'other_levies_projection' =>  'nullable',
            'other_levies_period_of_collection' =>  'nullable',
            'other_levies_total_amount' =>  'nullable',
            'other_levies_total_accumulation' =>  'nullable',
            'capital_projects' =>  'nullable',
            'recurrent_expenses' =>  'nullable',
            'maintenance' =>  'nullable',
            'misc' =>  'nullable',
            'expenses_grand_total' =>  'nullable',
            'spiritual_state' =>  'nullable',
            'challenges' =>  'nullable',
            'proposed_capital_project' =>  'nullable',
            'completed_capital_project' =>  'nullable',
            'president_signature' =>  'nullable',
            'gen_sec_signature' =>  'nullable',
            'evan_sec_signature' =>  'nullable',
            'fin_sec_signature' =>  'nullable',
            'zonal_pastor_approval' =>  'nullable',
            'zonal_pastor_affirmation' =>    'nullable',
            'field_pastor_approval' =>  'nullable',
            'field_pastor_comment' =>  'nullable',
            'communion' => 'nullable',
            'holy_communion_minister' => 'nullable',
            'holy_communion_minister_rank' => 'nullable',
            'holy_communion_attendance' => 'nullable',
        ]);

        return $data;
    }
}
