<?php

namespace App\Http\Controllers;

use DateTime;
use DatePeriod;
use App\Models\Reports;
use DateInterval;
use App\Models\Stakeholder;
use Illuminate\Http\File;
use App\Exports\ExportPop;
use App\Models\StakeholderPayment;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class StakeholderPaymentController extends Controller
{
    public function exportPop(Request $request)
	{		
		return Excel::download(new ExportPop($request->campus, $request->year, $request->month), 'Financial report.xlsx');
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $count = 1;
    
        if(Auth::guard('stakeholder')->user()->role == 'President'){
            $payments = StakeholderPayment::with('report')->whereChapterId(Auth::guard('stakeholder')->user()->chapter_id)->orderBy('created_at', 'desc')->get();
        }

        if(Auth::guard('stakeholder')->user()->role == 'Zonal Pastor'){
            $payments = StakeholderPayment::with('report')->whereZoneId(Auth::guard('stakeholder')->user()->zone_id)->orderBy('created_at', 'desc')->get();
        }

        if(Auth::guard('stakeholder')->user()->role == 'Field Pastor'){
            $payments = StakeholderPayment::with('report')->whereFieldId(Auth::guard('stakeholder')->user()->field_id)->orderBy('created_at', 'desc')->get();
        }

        if(Auth::guard('stakeholder')->user()->role == 'Financial Secretary' || Auth::guard('stakeholder')->user()->role == 'Secretariat'){
            $payments = StakeholderPayment::with('report')->orderBy('created_at', 'desc')->get();
        }

        $years = array_combine(range( date('Y'), date('2020')), range(date('Y'), date('2020')));
       
        $months = $this->getMonths();

        return view('stakeholder.proof_of_payment', compact('payments', 'count', 'years', 'months'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		if (Auth::guard('stakeholder')->user()->role == 'President') {
            $reports = Reports::whereDoesntHave('stakeholderpayment')->whereChapterId(Auth::guard('stakeholder')->user()->chapter_id)->orderBy('created_at', 'desc')->get();
            			
		}
        else if (Auth::guard('stakeholder')->user()->role == 'Field Pastor') {
            $reports = Reports::whereFieldId(Auth::guard('stakeholder')->user()->field_id)->orderBy('created_at', 'desc')->get();		
		} 

        else if (Auth::guard('stakeholder')->user()->role == 'Zonal Pastor') {
            $reports = Reports::whereZoneId(Auth::guard('stakeholder')->user()->zone_id)->orderBy('created_at', 'desc')->get();		
		} 

        else if (Auth::guard('stakeholder')->user()->role == 'Secretariat' || Auth::guard('stakeholder')->user()->role == 'Financial Secretary') {
            $reports = Reports::orderBy('created_at', 'desc')->get();		
		} 

        $descriptions = $this->getDescriptions();

        return view('stakeholder.createpop', compact('reports', 'descriptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if($request->report == null && $request->description == null){
            return back()->with('error', 'Associated Report and Description cannot be empty at the same time');
        }

        $data = $this->validate($request, [
            'report' => 'nullable',
            'description' => 'nullable',
            'image' => 'required',
            'amount' => 'required|numeric',
            'insession' => 'required',
        ]);

        if($request->has('image')){
            $file = date('d-M-Y-s') . '-' . pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            $fileextension = $request->image->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('paymentproof', new File($request->image->path()), $file.'.'.$fileextension);
            
            $filePath = $file . '.' . $fileextension;
        }

        $report = Reports::select('chapter_id', 'zone_id', 'field_id', 'month', 'year')->whereId($data['report'])->first();
    
        $payment = StakeholderPayment::create([
            'report_id' => $data['report'],
            'chapter_id' => $report->chapter_id ?? Auth::guard('stakeholder')->user()->chapter->id,
            'zone_id' => $report->zone_id ?? Auth::guard('stakeholder')->user()->zone->id,
            'field_id' => $report->field_id ?? Auth::guard('stakeholder')->user()->field->id,
            'amount' => $request->amount,
            'image' => $filePath,
            'month' => $report->month ?? date('m'),
            'year' => $report->year ?? date('Y'),
            'description' => $data['description'] ?? null,
            'insession' => $data['insession'],
        ]);

        //Send mail to Fin Sec
        $secretary = Stakeholder::whereRole('Financial Secretary')->wherePortfolio('Fin Sec')->first();
            if($secretary){
                $data = [
                    'type' => 'pop',
                    'addressee' => $secretary->name,
                    'chapter' => $report->chapter->name,
                    'date' => date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year,
                    'amount' => $payment->amount,
                ];

                Mail::to($secretary->email)->send(new NotificationEmail($data));
            }
        return redirect(route('stakeholderpayment.index'))->with('message', 'Operation Successful');
  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StakeholderPayment  $stakeholderPayment
     * @return \Illuminate\Http\Response
     */
    public function show(StakeholderPayment $stakeholderPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StakeholderPayment  $stakeholderPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(StakeholderPayment $stakeholderPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StakeholderPayment  $stakeholderPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StakeholderPayment $stakeholderPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StakeholderPayment  $stakeholderPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(StakeholderPayment $stakeholderPayment)
    {
    
    }

    public function downloadPop($id){
        $file = StakeholderPayment::find($id)->image;
        $realpath = base_path() . '/uploads/paymentproof'. '/' .$file;
        return response()->download($realpath);
       
    }

    public function delete($id)
    {
        $payment = StakeholderPayment::findorfail($id);
        if (file_exists(base_path() . '/uploads/paymentproof' . '/' . $payment->image ))
        unlink( base_path() . '/uploads/paymentproof' . '/' . $payment->image );

        $payment->delete();

        return back()->with('message', 'Operation succesful');
        
    }
}
