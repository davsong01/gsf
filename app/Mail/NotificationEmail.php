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

        if(isset($this->data['type']) && in_array($this->data['type'], ['twodaysnecbirthdaynotification', 'birthdaynotification', 'onedaynecbirthdaynotification', 'threedaysnecbirthdaynotification'])){
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
            ->attach($this->data['banners']);
        }

        if (!isset($this->data['type']) && $this->data['type'] == 0) {
            return $this->markdown('emails.notification')
            ->subject($this->data['subject']);
        }

        if (isset($this->data['type']) && $this->data['type'] == 'admin_donation_notification') {
            return $this->markdown('emails.donation')
            ->subject('New Donation');
        }

        if (isset($this->data['type']) && $this->data['type'] == 'donator_notification') {
            return $this->markdown('emails.donation')
            ->subject('Thank you for your Donation');
        }

        if (isset($this->data['type']) && $this->data['type'] == 'new_registration') {
            return $this->markdown('emails.welcomeMail')
            ->subject('New Registration');
        }
        
        if (isset($this->data['type']) && $this->data['type'] == 'conference_registration_welcome_mail') {
            return $this->markdown('emails.welcomeMail')->subject('Thank you for registering');
        }

        if (isset($this->data['type']) && $this->data['type'] == 'conference_bulk_email') {
            return $this->markdown('emails.welcomeMail')->subject($this->data['subject']);
        }

        if (
            isset($this->data['type']) &&
            $this->data['type'] === 'report_email'
        ) {
            $mail = $this->markdown('emails.generic')
                ->subject($this->data['subject']);

            foreach ((array) ($this->data['attachments'] ?? []) as $file) {
                if (is_string($file)) {
                    $mail->attach($file);
                }
            }

            return $mail;
        }
    }
}
