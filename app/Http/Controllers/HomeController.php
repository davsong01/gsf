<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Payout;
use App\Chapter;
use App\Setting;
use App\TempUser;
use Carbon\Carbon;
use App\Stakeholder;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

    public function cron(){
        //All Notifiable emails
        $notifiables = ['davsong16@gmail.com', 'abokiogbeni@gmail.com', 'princedamab19057@gmail.com', 'oyedepokds@gmail.com'];
        //Get all stakeholders 
        $stakeholders = Stakeholder::all();

        foreach($stakeholders as $stakeholder){
            if($stakeholder->day == date('d') && ($stakeholder->month == date('m'))){
                $data['name'] = $stakeholder->name;
                $data['type'] = 'birthdaynotification';
                $data['portfolio'] = $stakeholder->portfolio;
                foreach($notifiables as $notifiable){
                    Mail::to($notifiable)->send(new NotificationEmail($data));
                }
            }
        }
       
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
