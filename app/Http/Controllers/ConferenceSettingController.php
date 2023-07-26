<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Hostel;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;

class ConferenceSettingController extends Controller
{
    public function index()
    {
        if(auth()->user()->role== 1){
            $setting = $this->conferenceEdition();
            return view('conference_management.admin.settings.edit', compact('setting'));
        }
    }

    public function update(Request $request, Setting $conferencesetting)
    {
        $this->validate($request, [
            'conference_theme' => 'required',
            'registration_fee' => 'required|numeric',
            'official_email' => 'required|email',
            'alumni_registration_fee' => 'required|numeric',
            'new_alumni_registration_fee' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'close_registration' => 'required|date',
            'conference_overview' => 'required'
        ]);

        $conferencesetting->update($request->all());

        return back()->with('message', 'Applicaion settings succesfully updated');
    }

    public function resetData(){
        //Delete all payments
        Payment::truncate();
        //Empty Foodstands
        Food::truncate();
        //Empty Hostels
        Hostel::truncate();

        return back()->with('message', 'You have successfully reset the system, Users personal data were not deleted');
    }

}
