<?php

namespace App\Http\Controllers;

use App\Moels\Setting;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;

class ConferenceController extends Controller
{
    public function index()
    {
        $chapters = Chapter::orderBy('name')->get();
        $setting = ConferenceEdition::where('status', 'active')->where('close_registration', '>', date('Y-m-d'))->first();
        $frontend = GeneralSetting::first()->frontend_template;
        
        $conference_year = Carbon::parse($setting->start_date)->year;
        $alumnis_amount = [
            'alumni_registration_fee' => $setting->alumni_registration_fee,
            'new_alumni_registration_fee' => $setting->new_alumni_registration_fee
        ];

        return view('frontend.conference.template'. $setting->template_id.'.welcome');
        
    }
    
    public function thankyou()
    {
        return view('frontend.conference.thankyou');
    }

    public function  necRegistration()
    {
        $chapters = Chapter::orderBy('name')->get();
        $setting =  $this->conferenceEdition();
        $conference_year = Carbon::parse($setting->start_date)->year;

        return view('frontend.conference.necregistration', compact('conference_year'));
    }
}
