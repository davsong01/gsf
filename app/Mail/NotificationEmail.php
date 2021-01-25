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
          
    }
}
