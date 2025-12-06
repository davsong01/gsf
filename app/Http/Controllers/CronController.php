<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nec;
use App\Models\TempUser;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Models\CriticalEmail;
use App\Mail\NotificationEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PaymentController;
use App\Services\EmailService;

class CronController extends Controller
{
    public function cron(){
        //All Notifiable emails
        $notifiables = ['princedamab19057@gmail.com', 'oyedepokds@gmail.com', 'gsfnationalpublicity@gmail.com'];
        //Get all stakeholders 
        $stakeholders = Stakeholder::limit(10)->get();
     
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

    public function birthdayReminderForNec($days)
    {
        //All Notifiable emails
        $notifiables = ['princedamab19057@gmail.com', 'oyedepokds@gmail.com', 'gsfnationalpublicity@gmail.com'];
        //Get all stakeholders 
        $stakeholders = Nec::all();
        $twoDaysBefore = Carbon::now()->addDays($days);
        $twoDaysBefore = $twoDaysBefore->format('d');
        if($days == 1){
            $type = 'onedaynecbirthdaynotification';
        }
        if ($days == 2) {
            $type = 'twodaysnecbirthdaynotification';
        }
        if ($days == 3) {
            $type = 'threedaysnecbirthdaynotification';
        }
       
        foreach ($stakeholders as $stakeholder) {
            if(!empty($stakeholder->bday)){
                $date = explode('/',$stakeholder->bday);
                $day = $date[0];
                $month = $date[1];

                if ($day == $twoDaysBefore && $month == date('m')) {
                    $data['name'] = $stakeholder->name;
                    $data['bday'] = date('Y').'/'.$stakeholder->bday;
                    $data['type'] = $type;
                    $data['portfolio'] = $stakeholder->office;
    
                    foreach ($notifiables as $notifiable) {
                        Mail::to($notifiable)->send(new NotificationEmail($data));
                    }
                }
            }
        }
    }

    public function emailCron($pick = 10)
    {
        $hourlyRate = 150;

        // Normalize requested pick
        $pick = (int) $pick;
        if ($pick < 1) {
            $pick = 10; // default fallback
        }

        $oneHourAgo = Carbon::now()->subHours(1);
        ini_set('max_execution_time', 600); //5 minutes

        // Check how many have been sent within the hour
        $sentWithinTheHour = CriticalEmail::where('status', 1)
            ->whereBetween('sent_at', [$oneHourAgo, now()])
            ->count();

        // If we've already hit hourly limit, stop
        if ($sentWithinTheHour >= $hourlyRate) {
            \Log::info(now() . ' : Hourly email rate exceeded on server. ' . $sentWithinTheHour . ' emails already sent this hour');
            echo ('Hourly email rate exceeded on server. ' . $sentWithinTheHour . ' emails already sent this hour');
            return;
        }

        // Respect requested pick but cap to remaining allowance
        $allowed = $hourlyRate - $sentWithinTheHour;
        $toFetch = min($pick, $allowed);

        if ($toFetch <= 0) {
            \Log::info(now() . ' : No emails allowed to be sent at this time. Allowed: ' . $allowed . ' Requested: ' . $pick);
            echo 'No emails can be sent at this time';
            return;
        }

        $emails = CriticalEmail::with('settings')->where('status', 0)->whereNull('sent_at')->take($toFetch)->get();
        $count = 0;
        
        foreach ($emails as $email) {
            $data['settings'] = $email->settings ?? null;
            $data['type'] = $email->type;
            $data['recipient'] = $email->recipient;
            $data['content'] = $email->content;
            $data['subject'] = $email->subject;
            $data['attachments'] = $email->attachments;
            
            $res = EmailService::sendEmail($data);
            
            if (isset($res['message']) && $res['message'] == 'success') {
                $count++;
                $email->status = 1;
                $email->sent_at = now();
                $email->errors = NULL;
                $email->save();
            } else {
                $email->errors = $res['error'] ?? null;
                $email->save();
            }
        }

        echo $count . ' emails sent successfully';
    }

}
