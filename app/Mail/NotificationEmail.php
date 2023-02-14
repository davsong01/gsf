<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(isset($this->data['type']) && $this->data['type'] == 'payout_notification'){
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }
        
        if(isset($this->data['type']) && $this->data['type'] == 'payment_made'){
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }

        if(isset($this->data['type']) && $this->data['type'] == 'post_approval'){
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }

        if(isset($this->data['type']) && $this->data['type'] == 'post_unapproval'){
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }

        if(isset($this->data['type']) && $this->data['type'] == 'zone'){
            return $this->markdown('emails.notification')
            ->subject('Report Notification');
        }

        if(isset($this->data['type']) && $this->data['type'] == 'birthdaynotification'){
            return $this->markdown('emails.notification')
            ->subject('Its '. $this->data['name'] . '\'s Birthday');
        }

        if(isset($this->data['type']) && $this->data['type'] == 'zonalRejection'){
            return $this->markdown('emails.notification')
            ->subject('GSF Report Rejected by Zonal Pastor!');
        }
        if(isset($this->data['type']) && $this->data['type'] == 'fieldRejection'){
            return $this->markdown('emails.notification')
            ->subject('GSF Report Rejected by Field Pastor!');
        }
        if(isset($this->data['type']) && $this->data['type'] == 'nationalRejection'){
            return $this->markdown('emails.notification')
            ->subject('GSF Report Rejected by National Secretariat');
        }
        if(isset($this->data['type']) && $this->data['type'] == 'resend'){
            return $this->markdown('emails.notification')
            ->subject('Resent Report');
        }

        if(isset($this->data['type']) && $this->data['type'] == 'pop'){
            return $this->markdown('emails.notification')
            ->subject('New Payment report from '. $this->data['chapter']);
        }
        
        if(isset($this->data['type']) && $this->data['type'] == 'email'){
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }

        if(isset($this->data['type']) && $this->data['type'] == 'emailReport'){
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }
        
        if(!isset($this->data['type']) && $this->data['type'] == 0){
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }

        if(!isset($this->data['type']) && $this->data['type'] == 'Event'){
            return $this->markdown('emails.notification')
            ->subject($this->data['subject'])
            ->attach($data['banners']);
        }

        if (!isset($this->data['type']) && $this->data['type'] == 0) {
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }

        if (isset($this->data['type']) && $this->data['type'] == 'admin_donation_notification') {
            return $this->markdown('emails.donation')
            ->subject('New Donation');
        }

        if (isset($this->data['type']) && $this->data['type'] == 'admin_registration_notification') {
            return $this->markdown('emails.admin')
            ->subject('New Registration');
        }
      
        if (isset($this->data['type']) && $this->data['type'] == 'welcome_mail') {
            return $this->markdown('emails.welcomeMail')->subject('Thank you for registering');
        }

    }
}
