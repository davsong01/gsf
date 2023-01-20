<?php

namespace App\Http\Controllers;

use App\Chapter;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConferenceController extends Controller
{
    public function index()
    {
        $chapters = Chapter::orderBy('name')->get();
        $setting = Setting::first();
        $conference_year = Carbon::parse($setting->start_date)->year;
        $alumnis_amount = [
            'alumni_registration_fee' => $setting->alumni_registration_fee,
            'new_alumni_registration_fee' => $setting->new_alumni_registration_fee
        ];

        return view('frontend.conference.welcome', compact('chapters', 'setting', 'conference_year', 'alumnis_amount'));
    }

    public function thankyou()
    {
        return view('frontend.conference.thankyou');
    }

    public function  necRegistration()
    {
        $chapters = Chapter::orderBy('name')->get();
        $setting = Setting::first();
        $conference_year = Carbon::parse($setting->start_date)->year;

        return view('frontend.conference.necregistration', compact('conference_year'));
    }


}
