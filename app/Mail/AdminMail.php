<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function build()
    {
        if($this->data['type'] == 5){
            return $this->markdown('emails.donation')
            ->subject('New Donation');
        }else{
        return $this->markdown('emails.admin')
            ->subject('New Registration');
        }
    }
        
}
