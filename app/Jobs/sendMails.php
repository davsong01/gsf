<?php

namespace App\Jobs;

use App\Email;
use App\Setting;
use Illuminate\Bus\Queueable;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class sendMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $data;
    private $recipients;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $timeout = 300;     
    public $retryAfter = 280;  
    public $tries = 1;    
    // public $tries = 2;    

    public function __construct($data, $recipients)
    {
        $this->data = $data;
        $this->recipients = $recipients;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Todo Apply chunk recipients here

        foreach($this->recipients as $d){
            $data['name'] = $d['name'];
            $this->data = array_merge($this->data, $data);
            Mail::to($d['email'])->send(new NotificationEmail($this->data));   
        }

        //Send mail to admin
        $admin = \App\Setting::first()->value('official_email');
       
        // $admin = Setting::value('official_email');
        $data['type'] = 'emailReport';
        $data['name'] = 'Admin';
        $data['subject'] = 'Emails sent';
        $data['count'] = count($this->recipients);

        $this->data = array_merge($this->data, $data);

        Mail::to($admin)->send(new NotificationEmail($this->data));  
      
    }
}
