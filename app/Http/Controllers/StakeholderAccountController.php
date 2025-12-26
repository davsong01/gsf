<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Reports;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\StakeholderReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StakeholderAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function dashboard()
    {
        $count = 1;
        // return redirect(route('stakeholder.login'));
        if (!auth::guard('stakeholder')->check()) return redirect(route('stakeholders.login'));
        $role = Auth::guard('stakeholder')->user()->role_id;

        if (in_array($role, chapterStakeholders())) {
            $reports = StakeholderReport::whereChapterId(Auth::guard('stakeholder')->user()->chapter_id)->orderBy('created_at', 'desc')->get();
        } elseif (in_array($role, fieldStakeholders())) {
            $reports = StakeholderReport::whereFieldId(Auth::guard('stakeholder')->user()->field_id)->orderBy('created_at', 'desc')->get();
        } elseif (in_array($role, zoneStakeholders())) {
            $reports = StakeholderReport::whereZoneId(Auth::guard('stakeholder')->user()->zone_id)->orderBy('created_at', 'desc')->get();
        } elseif (in_array($role, secretariatStakeholders())) {
            $reports = StakeholderReport::orderBy('created_at', 'desc')->get();
        }

        return view('stakeholder.dashboard', compact('reports', 'count'));
    }
    
    public function profile()
    {
        return view('stakeholder.profile');
    }

    public function saveProfile(Request $request)
    {
        //Handle Password
        if ($request['password']) {
			$password = Hash::make($request['password']);
		} else {
			$password = Hash::make($request['12345@GSF2021']);
		}

        //Handle all signatures
        if($request->has('signature')){
            if (file_exists(base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->signature))
            unlink( base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->signature);
            $signaturefilename = date('d-M-Y-s') . '-' . pathinfo($request->signature->getClientOriginalName(), PATHINFO_FILENAME);
            $signatureextension = $request->signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->signature->path()), $signaturefilename.'.'.$signatureextension);
            
            $signature = $signaturefilename . '.' . $signatureextension;
        }else{
            $signature = Auth::guard('stakeholder')->user()->signature;
        }

        if($request->has('gen_sec_signature')){
            if (!is_null(Auth::guard('stakeholder')->user()->gen_sec_signature) && file_exists(base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->gen_sec_signature))
            unlink( base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->gen_sec_signature);
            $gen_sec_signaturefilename = date('d-M-Y-s') . '-' . pathinfo($request->gen_sec_signature->getClientOriginalName(), PATHINFO_FILENAME);
            $gen_sec_signatureextension = $request->gen_sec_signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->gen_sec_signature->path()), $gen_sec_signaturefilename.'.'.$gen_sec_signatureextension);
            
            $gen_sec_signature = $gen_sec_signaturefilename . '.' . $gen_sec_signatureextension;
        }else{
            $gen_sec_signature = Auth::guard('stakeholder')->user()->gen_sec_signature;
        }

        if($request->has('fin_sec_signature')){
            if (!is_null(Auth::guard('stakeholder')->user()->fin_sec_signature) && file_exists(base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->fin_sec_signature))
            unlink( base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->fin_sec_signature);
            $fin_sec_signaturefilename = date('d-M-Y-s') . '-' . pathinfo($request->fin_sec_signature->getClientOriginalName(), PATHINFO_FILENAME);
            $fin_sec_signatureextension = $request->fin_sec_signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->fin_sec_signature->path()), $fin_sec_signaturefilename.'.'.$fin_sec_signatureextension);
            
            $fin_sec_signature = $fin_sec_signaturefilename . '.' . $fin_sec_signatureextension;
        }else{
            $fin_sec_signature = Auth::guard('stakeholder')->user()->fin_sec_signature;
        }
        
        if($request->has('evang_sec_signature')){
            if (!is_null(Auth::guard('stakeholder')->user()->evang_sec_signature) && file_exists(base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->evang_sec_signature))
            unlink( base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->evang_sec_signature);

            $evang_sec_signaturefilename = date('d-M-Y-s') . '-' . pathinfo($request->evang_sec_signature->getClientOriginalName(), PATHINFO_FILENAME);
            $evang_sec_signatureextension = $request->evang_sec_signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->evang_sec_signature->path()), $evang_sec_signaturefilename.'.'.$evang_sec_signatureextension);
            
            $evang_sec_signature = $evang_sec_signaturefilename . '.' . $evang_sec_signatureextension;
            
        }else{
            $evang_sec_signature = Auth::guard('stakeholder')->user()->evang_sec_signature;
        }
        Auth::guard('stakeholder')->user()->name = $request->name;
        Auth::guard('stakeholder')->user()->gen_sec_signature = $gen_sec_signature;
        Auth::guard('stakeholder')->user()->password = $password;
        Auth::guard('stakeholder')->user()->signature = $signature;
        Auth::guard('stakeholder')->user()->fin_sec_signature = $fin_sec_signature;
        Auth::guard('stakeholder')->user()->evang_sec_signature = $evang_sec_signature;
        Auth::guard('stakeholder')->user()->phone = $request->phone;
        Auth::guard('stakeholder')->user()->email = $request->email;
        Auth::guard('stakeholder')->user()->day = $request->day;
        Auth::guard('stakeholder')->user()->month = $request->month;
        Auth::guard('stakeholder')->user()->year = $request->year;

        Auth::guard('stakeholder')->user()->save();
        
        return back()->with('message', 'Update Successful');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
