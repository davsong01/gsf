<?php
namespace App\Services;

use App\Models\CriticalEmail;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService {
    public static function getContent($type, $transaction=null, $extraData=null)
    {
        $subject = '';
        $content = '';

        if(!empty($transaction)){
            $conferenceTheme = $transaction->edition->conference_theme ?? 'GSF National Conference';
            $user = $transaction->user ?? null;
            $fields = $transaction->allocationFields;

            $registrationDetails = "
            <strong>Name:</strong> {$transaction->name}<br>
            <strong>Email:</strong> {$transaction->email}<br>
            <strong>Phone:</strong> {$transaction->phone}<br>
            <strong>Amount Paid:</strong> &#8358;" . number_format($transaction->amount_paid ?? $transaction->amount ?? 0) . "<br><br>
            <strong>Service Charge:</strong> &#8358;" . number_format($transaction->provider_charge ?? 0) . "<br><br>
            <strong>Total Amount Paid:</strong> &#8358;" . number_format($transaction->total_amount ?? (($transaction->amount_paid ?? 0) + ($transaction->provider_charge ?? 0))) . "<br><br>";

            $allocationDetails = '';
            if (!empty($transaction->hostel_id)) {
                $allocationDetails .= "<strong>Allocated Hostel:</strong> {$transaction->hostel->name}<br>Hostel Allocation Number: {$transaction->hostel_allocation_number}<br>";
            }
            if (!empty($transaction->food_id)) {
                $allocationDetails .= "<br><strong>Allocated Service Point:</strong> {$transaction->food->name}<br>Service Point Allocation Number: {$transaction->service_point_allocation_number}<br><br>";
            }

            $loginDetails = $user ? "
            <strong>Login ID:</strong> {$user->family_id}<br>
            <strong>Password:</strong> {$transaction->phone}<br><br>
            You can login and change your password for security reasons.<br><br>
            <a style='color:white;text-decoration:none;background-color:#29166f;padding:7px;border-radius:5px;' href='" . route('login') . "'>Login</a><br><br>" : '';
        }

        switch ($type) {
            case 'conference_registration_welcome_mail':
                $subject = "Welcome to {$conferenceTheme}";
                $content = "
                Dear {$transaction->name}, <br><br>
                Your registration for {$conferenceTheme} is successful. <br><br>
                Below are the details of your registration: <br><br>
                {$registrationDetails}
                <strong>Allocation Details:</strong><br>{$allocationDetails}
                Kindly login to your dashboard to view your profile and print your ID card:<br><br>
                {$loginDetails}
                Thanks.";
                break;

            case 'new_registration':
                $subject = 'New Conference Registration Notification';
                $prefix = match ($transaction->level) {
                    'Moderator', 'Participant' => "A participant has just registered for the {$conferenceTheme}.<br><br>",
                    'Alumni' => "An Alumni has just registered for the {$conferenceTheme}.<br><br>",
                    default => "A new registration has been made.<br><br>",
                };

                $content = "
                Dear Admin, <br><br>{$prefix}
                {$registrationDetails}
                <strong>Family ID:</strong> {$transaction->user->family_id}<br>
                Thanks.";
                break;

            case 'admin_donation_notification':
                $subject = 'New Donation Received for Conference';
                $content = "
                Dear Admin,<br><br>
                A new donation has just been made for the {$conferenceTheme}.<br><br>
                {$registrationDetails}
                <strong>Payment Mode:</strong> {$transaction->payment_type}<br>
                <strong>Transaction ID:</strong> {$transaction->transid}<br><br>
                Thanks.";
                break;

            case 'donator_notification':
                $subject = 'Thank You for Your Donation';
                $content = "
                Dear {$transaction->name},<br><br>
                Thank you for your donation of &#8358;" . number_format($transaction->amount ?? 0) . " towards {$conferenceTheme}.<br><br>
                Your support is deeply appreciated.<br><br>
                <strong>Transaction ID:</strong> {$transaction->transid}<br><br>
                Thanks.";
                break;

            case 'donation_thank_you_mail':
                $subject = 'Thank You for Supporting GSF';
                $content = "
                Dear {$transaction->name},<br><br>
                Thank you for your generous contribution of &#8358;" . number_format($transaction->amount ?? 0) . " to GSF.<br><br>
                God bless you abundantly.<br><br>
                <strong>Transaction ID:</strong> {$transaction->transid}<br><br>
                Thanks.";
                break;

            case 'admin_donation_general_notification':
                $subject = 'New GSF Payment Notification';
                $content = "
                Dear Admin,<br><br>
                A new payment for {$transaction->type} has been received.<br><br>
                {$registrationDetails}
                <strong>Type:</strong> {$transaction->type}<br>
                <strong>Status:</strong> {$transaction->membership_status}<br>
                <strong>Date:</strong> {$transaction->created_at}<br>
                <strong>Transaction ID:</strong> {$transaction->transid}<br><br>
                Thanks.";
                break;
            case 'conference_bulk_email';
                $subject = $extraData['subject'] ?? null;
                $content = $extraData['content'] ?? null;
                break;
            default:
                $subject = 'GSF Notification';
                $content = 'No content available for this notification type.';
                break;
        }

        return [
            'subject' => $subject,
            'content' => $content,
        ];
    }

    public static function logEmail($data)
    {
        $type = $data['type'];
        $transaction = $data['transaction'] ?? null;

        $emailContent = self::getContent($type, $transaction, $data);

        $subject = $data['subject'] ?? $emailContent['subject'];
        $content = $data['content'] ?? $emailContent['content'];

        $insert = [];

        if (!empty($data['priority']) && $data['priority'] == 1) {
            $type = $data['type'];

            $subject = $data['subject'] ?? $emailContent['subject'];
            $content = $data['content'] ?? $emailContent['content'];

            $record = CriticalEmail::create([
                'recipient' => $data['recipient'],
                'type' => $type,
                'conference_edition_id' => $transaction->conference_edition_id ?? null,
                'subject' => $subject,
                'attachments' => !empty($data['attachments']) ? json_encode($data['attachments']) : null,
                'content' => $content,
            ]);

            $data['settings'] = $record->settings ?? null;
            $data['type'] = $record->type;
            $data['recipient'] = $record->recipient;
            $data['content'] = $record->content;
            $data['subject'] = $record->subject;
            $data['attachments'] = $record->attachments;

            $res = EmailService::sendEmail($data);

            if (isset($res['status'])) {
                $record->update([
                    'status' => 1,
                    'sent_at' => now(),
                    'errors' => NULL,
                ]);
            } else {
                $record->update([
                    'errors' => $res['error'] ?? null,
                ]);
            }


            return [
                'status' => $res['status'] ?? false,
                'record' => $record->fresh(),
                'response'  => $res
            ];
        }

        if(in_array($data['type'], ['conference_bulk_email'])){
            $recipients = $data['recipients'] ?? null;
            foreach($recipients as $recipient){
                $insert[] = [
                    'recipient' => $recipient['email'] ?? $recipient,
                    'type' => $type,
                    'conference_edition_id' => $transaction->conference_edition_id ?? null,
                    'subject' => $subject,
                    'attachments' => !empty($data['attachments']) ? json_encode($data['attachments']) : null,
                    'content' => $content,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        } elseif (in_array($data['type'], ['report_email'])){
            $insert = $data['recipients'];
        }else{
            $insert[] = [
                'recipient' => in_array($type, ['new_registration']) ? $transaction->edition->official_email : $transaction->email,
                'type' => $type,
                'conference_edition_id' => $transaction->conference_edition_id,
                'subject' => $subject,
                'attachments' => !empty($data['attachments']) ? json_encode($data['attachments']) : null,
                'content' => $content,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        CriticalEmail::insert($insert);

    }

    /**
     * Send all registration-related emails.
     */
    public static function sendRegistrationEmails($transaction)
    {
        $emailData['transaction'] = $transaction;

        // Welcome email to participant
        $emailData['type'] = 'conference_registration_welcome_mail';
        self::logEmail($emailData);

        // Notify admin/new registration
        $emailData['type'] = 'new_registration';
        self::logEmail($emailData);
    }


    public static function sendEmail($data, $preview = false)
    {
        try {
            if ($preview) {
                return (new NotificationEmail($data))->render();
            }

            if(env('APP_ENV') == 'local'){
                $data['recipient'] = 'davsong16@gmail.com';
            }

            Mail::to($data['recipient'])->send(new NotificationEmail($data));

            return [
                'status' => true,
                'message' => 'success'
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
