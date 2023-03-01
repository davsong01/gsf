<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Stakeholder;
use App\CriticalEmail;
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

    public function emailCron($pick = 10)
    {
        $hourlyRate = 150;
        
        $oneHourAgo = Carbon::now()->subHours(1);
        ini_set('max_execution_time', 600); //5 minutes
       
        // Check how many have been sent within the hour
        $sentWithinTheHour = CriticalEmail::where('status', 1)->whereBetween('sent_at', [$oneHourAgo, now()])->count();
        // \Log::info('Emails sending start at: ' .now());
        if ($sentWithinTheHour >= $hourlyRate) {
            \Log::info(now() . ' : Hourly email rate exceeded on server. ' . $sentWithinTheHour . ' emails already sent this hour');
            echo ('Hourly email rate exceeded on server. ' . $sentWithinTheHour . ' emails already sent this hour');
        } else {
            $pick = $hourlyRate - $sentWithinTheHour;

            $emails = CriticalEmail::where('status', 0)->whereNull('sent_at')->take($pick)->get();
            $count = 0;
            
            foreach ($emails as $email) {
                $data['type'] = $email->type;
                $data['recipient'] = $email->recipient;
                $data['content'] = $email->content;
                $data['subject'] = $email->subject;
                $data['attachments'] = $email->attachments;
                
                $res = $this->sendEmail($data);
               
                if (($res['message'] && $res['message'] == 'success')) {
                    $count++;
                    $email->status = 1;
                    $email->sent_at = now();
                    $email->save();
                } else {
                    $email->errors = $res['error'];
                    $email->save();
                }
            }
            echo $count . ' emails sent successfully';
        }
    }

}
