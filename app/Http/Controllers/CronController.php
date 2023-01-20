<?php

namespace App\Http\Controllers;

use App\Stakeholder;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class CronController extends Controller
{
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

}
