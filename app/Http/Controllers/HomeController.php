<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Payout;
use App\Chapter;
use App\Setting;
use App\TempUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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

        return view('welcome', compact('chapters', 'setting', 'conference_year', 'alumnis_amount'));
    }

    public function thankyou()
    {
        return view('thankyou');
    }

     public function  necRegistration()
    {
        $chapters = Chapter::orderBy('name')->get();
        $setting = Setting::first();
        $conference_year = Carbon::parse($setting->start_date)->year;

        return view('necregistration', compact('conference_year'));
    }

    public function temp(){

       dd('sdsd');
    }

}
