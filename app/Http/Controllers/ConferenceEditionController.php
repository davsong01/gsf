<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Material;
use App\Models\ConferenceEdition;
use Illuminate\Http\Request;

class ConferenceEditionController extends Controller
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
        if (auth()->user()->role == 1) {
            return view('conference_management.admin.editions.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            "status" => "required",
            "conference_theme" => "required",
            "registration_fee" => "required",
            "official_email" => "required",
            "new_alumni_registration_fee" => "required|numeric",
            "start_date" => "required",
            "alumni_registration_fee" => "required",
            "end_date" => "required",
            "close_registration" => "required",
            "random_hostel" => "required",
            "random_foodstand" => "required",
            "reg_prefix" => "required",
            "conference_overview" => "required",
            "PAYSTACK_SECRET_KEY" => "required",
            "PAYSTACK_PUBLIC_KEY" => "required",
            "MERCHANT_EMAIL" => "required",
        ]);
        
        // Check if existing active
        $active = ConferenceEdition::where('status','active')->count();
        
        if(isset($active) && $active > 0){
            $data['status'] = 'inactive';
        }

        ConferenceEdition::create($data);
        return redirect(route('conferenceeditions.index'))->with('message', 'Operation Successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function show(ConferenceEdition $conferenceEdition, $id)
    {
        if (auth()->user()->role == 1) {
            $edition = ConferenceEdition::with(['payments','donations'])->find($id);
            
            $registered_participants = $edition->payments->count();
            $pending_registration = $edition->payments->where('registration_status', 'Pending')->count();
            $total = $edition->payments->sum('amount_paid');
            $completed_registration = $edition->payments->where('registration_status', 'Complete')->count();
            $donations = Donation::where('conference_edition_id',$id)->sum('amount');
            $materials = Material::where('conference_edition_id',$id)->count();
            
            return view('conference_management.admin.index', compact('registered_participants', 'pending_registration', 'completed_registration', 'total', 'donations', 'materials', 'edition'));
        }
    }

    public function clone(ConferenceEdition $conferenceEdition, $id)
    {
        if (auth()->user()->role == 1) {
            $edition = ConferenceEdition::find($id);
            $new = $edition->replicate();
            $new->status = 'inactive';
            $new->conference_theme = $edition->conference_theme . '_copy';
            $new->save();
           
            return back()->with('message','Copy Succesfull');
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function edit(ConferenceEdition $conferenceEdition, $id)
    {
        if (auth()->user()->role == 1) {
            $edition = ConferenceEdition::find($id);
            return view('conference_management.admin.editions.edit', compact('edition'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConferenceEdition $conferenceEdition)
    {
        $edition = ConferenceEdition::find($request->id);
        
        if($request->has('ban')){
			$request['banner'] = $this->uploadImage($request->ban, 'frontend/img/site');
        }

        if ($request->has('logo')) {
			$request['conference_logo'] = $this->uploadImage($request->logo, 'frontend/img/site');
        }

        if ($request->has('favicon')) {
            $request['conference_favicon'] = $this->uploadImage($request->favicon, 'frontend/img/site');
        }
        
        $edition->update($request->except(['ban','logo','favicon']));
        return back()->with('message', 'Operation Successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $edition = ConferenceEdition::find($id);
        $edition->delete();
        return back()->with('message', 'Operation Successful');
    }
}
