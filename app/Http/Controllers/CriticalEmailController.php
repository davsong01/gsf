<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\CriticalEmail;
use App\Models\GeneralSetting;

class CriticalEmailController extends Controller
{
    public function getContent($data=null){
        switch ($data['type']) {
            case 'conference_registration_welcome_mail':
                $account = "<a style='color: white;text-decoration: none;background-color: #29166f;padding: 7px;border-radius: 5px;' href='". route('conferencemanagement.index', ['edition' => $data['conference_edition_id']])."'>Login</a>";
                
                $content = "Dear ".$data['name']. ", <br><br>
                    Your registration for GSF National conference is successful. <br><br>Below are the details of your registration <br><br>
                    <strong>Name: </strong>".$data['name']."<br>
                    <strong>Email: </strong>". $data['email']."<br>
                    <strong>Phone: </strong>". $data['phone']."<br>
                    <strong>Amount Paid: </strong> &#8358;". number_format($data['amount']) . "<br><br>
                    <strong>Allocation Details:</strong><br>";

                if(isset($data['allocated_hostel_data']) && !empty($data['allocated_hostel_data'])){
                    $content .= "<strong>Allocated Hostel: </strong>".$data['allocated_hostel_data']['hostel_name']."<br>
                    Hostel Allocation Number: ". $data['allocated_hostel_data']['hostel_allocation_number']."<br>";
                }

                if (isset($data['allocated_service_point_data']) && !empty($data['allocated_service_point_data'])) {
                    $content .= "<br><strong>Allocated Service Point: </strong>" . $data['allocated_service_point_data']['service_point_allocation_name'] . "<br>
                    Serivce Point Allocation Number: " . $data['allocated_service_point_data']['service_point_allocation_number'] . "<br><br>";
                }

                $content .= "Kindly login to you dashboard with the following details to view your profile and print ID card: <br><br><strong>Family ID: </strong>".$data['family_id']."<br>
                    <strong>Password: </strong>".$data['phone']. "<br><br>You can login and change your password for confidential reasons<br><br>". $account."<br><br>Thanks.";
                # code...
                break;
            case 'new_registration':
                if ($data['level'] == 'Moderator'|| $data['level'] == 'Participant') {
                    $prefix = "A participant has just registered for the GSF National Conference, Please find details below: <br><br>";
                };
                if ($data['level'] == 'Alumni') {
                    $prefix = "An Alumni has just registered for the GSF National Conference, Please find details below:<br><br>";
                };

                $content = "Dear Admin, <br><br>".$prefix."<strong>Name: </strong>" . $data['name'] . "<br>
                    <strong>Email: </strong>" . $data['email'] . "<br>
                    <strong>Phone: </strong>" . $data['phone'] . "<br>
                    <strong>Phone: </strong>" . $data[ 'family_id'] . "<br>
                    <strong>Chapter: </strong>" . $data['chapter'] . "<br>
                    <strong>Amount Paid: </strong> &#8358;" . $data['amount'] . "<br><br>Thanks";
                    
                break;

            case 'admin_donation_notification':
                $content = "Dear Admin, <br><br>A new donation has just been made for the ".$data['conference_theme']." conference. <br><br>Please find details below:<br><br>
                <strong>Name: </strong> ".$data['name']. "<br>
                <strong>Email: </strong> ".$data['email']. "<br>
                <strong>Phone: </strong> ".$data['phone']. "<br>
                <strong>Amount Paid: </strong> &#8358;".number_format($data['amount'])."<br>
                <strong>Payment Mode: </strong>".$data['payment_type']."<br>
                <strong>Transaction ID: </strong>".$data['transid']."<br><br>Thanks,<br>";
                # code...
                break;

            case 'donator_notification':
                $content = "Dear " . $data['name'] . "<br>
                Thank you for your donation of &#8358;" . number_format($data['amount']) . " for " . $data['conference_theme'] . " conference. <br><br>
                You are much appreciated<br><br>
                <strong>Transaction ID: </strong>" . $data['transid'] . "<br><br>Thanks,<br>";
                # code...
                break;
            case 'new_listing':
                $content = "<p>Dear Admin, <br>
                A new Listing has been submmitted on GSF Directory website, please find details below: <br><br>
                <strong>Name: </strong>" . $data['request']['name'] . "<br>
                <strong>Phone: </strong>" . $data['request']['phone'] . "<br>
                <strong>Email: </strong>" . $data['request']['email'] . "<br>
                <strong>Gender: </strong>" . $data['request']['gender'] . "<br>
                <strong>Portfolio: </strong>" . $data[ 'request']['portfolio'] . "<br>
                <strong>Chapter: </strong>" . $data['chapter'] . "<br>
                <strong>Matriculation year: </strong>" . $data['request']['matriculation_year'] . "<br>
                <strong>Graduation year: </strong>" . $data['request']['graduation_year'] . "<br>
                </p><br><br>
                Kindly sign in to approve listing
                ";
            break;
            case 'new_mewmber_listing':
                $level = $data['status'] == 0 ? 'Student' : 'Alumni';
                $content = "<p>Dear Admin, <br>
                A new Listing has been submmitted on GSF Directory website, please find details below: <br><br>
                <strong>Name: </strong>" . $data['name'] . "<br>
                <strong>Phone: </strong>" . $data['phone'] . "<br>
                <strong>Email: </strong>" . $data['email'] . "<br>
                <strong>Gender: </strong>" . $data['gender'] . "<br>
                <strong>Portfolio: </strong>" . $data['portfolio'] . "<br>
                <strong>Chapter: </strong>" . $data['chapter'] . "<br>
                <strong>Matriculation year: </strong>" . $data['matriculation_year'] . "<br>
                <strong>Graduation year: </strong>" . $data['graduation_year'] . "<br>
                <strong>Status: </strong>" . $level . "<br>

                </p><br><br>
                Kindly sign in to approve listing
                ";
            break;
            case 'new_mewmber_listing_multiple':
                $level = $data['status'] == 0 ? 'Student' : 'Alumni';
                $content = "<p>Dear Admin, <br>
                A new Listing has been submmitted on GSF Directory website via nultiple upload<br><br>
                
                </p><br><br>
                Kindly sign in to approve listings
                ";
            break;
            case 'alumni-upload';
                $content = "Dear " . $data['name'] . ", <br><br>
                    We have successfully received your details. <br><br>We are now processing your submission and you will will be notified via email once we complete the verification process. Thank you<br><br>
                    ";
            break;
            case 'donation_thank_you_mail':
                $content = "Dear " . $data['name'] . "<br>
                Thank you for your contribution of &#8358;" . number_format($data['amount']) . " to GSF. <br><br>
                You are much appreciated and God bless you<br><br>Please find transaction ID of this teansaction below: <br><br>
                <strong>Transaction ID: </strong>" . $data['transid'] . "<br><br>Thanks,<br>";
                # code...
                break;
            case 'admin_donation_general_notification':
                $content = "Dear Admin, <br><br>A new payment for " . $data['type'] . " has just been made. <br><br>Please find details below:<br><br>
                <strong>Name: </strong> " . $data['name'] . "<br>
                <strong>Email: </strong> " . $data['email'] . "<br>
                <strong>Phone: </strong> " . $data['phone'] . "<br>
                <strong>Type: </strong> " . $data['type'] . "<br>
                <strong>Status: </strong> " . $data['membership_status'] . "<br>
                <strong>Amount Paid: </strong> &#8358;" . number_format($data['amount']) . "<br>
                <strong>Date: </strong>" . $data['created_at'] . "<br>
                <strong>Transaction ID: </strong>" . $data['transid'] . "<br><br>Thanks,<br>";
                # code...
                break;
            case 'approve_listing':
                $account = "<a style='color: white;text-decoration: none;background-color: #29166f;padding: 7px;border-radius: 5px;' href='" . url('/').'/login'. "'>Login</a>";
                $content = "<p>Dear " . $data['name'].
                ", <br>
                Your details have been added to the GSF Directory website<br><br>
                Kindly login to you dashboard with the following details to re-view your profile: <br><br><strong>Family ID: </strong>" . $data['family_id'] . "<br>
                <strong>Password: </strong>" . $data['phone'] . "<br><br>You can login and change your password for confidential reasons<br><br>" . $account . "<br><br>Thanks.";
                break;
            case 'reject_listing':
                $content = "<p>Dear " . $data['name'] .
                ", <br>For some reason, trying to add your details to the GSF Directory website failed<br><br>
                If you think this is an error, kindly reach out to GSF IT support or reach us via email at: ".GeneralSetting::first()->value('official_email')." <br><br>Warm Regards.";
                break;
            case 'report_email':
                $content = $data['content'];
                break;
            default:
                # code...
                break;
            }
            
            return $content;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $emails = CriticalEmail::latest()->orderby('status','ASC')->get();
        $count = 1;
        return view('emails.emaillog', compact('emails','count'));
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CriticalEmail  $criticalEmail
     * @return \Illuminate\Http\Response
     */
    public function show(CriticalEmail $criticalEmail)
    {
        // Resend Email
        // $payment = Payment::with('user')->findOrFail($id);
        // $user = $payment->user;

        // $data['family_id'] = $user->family_id;
        // $data['name'] =  $user->name;
        // $data['email'] =  $user->email;
        // $data['phone'] =  $user->phone;
        // $data['amount'] =  $payment->amount_paid;
        $data['type'] = $criticalEmail->type;
        $data['recipient'] = $criticalEmail->recipient;
        $data['content'] = $criticalEmail->content;
        $data['subject'] = $criticalEmail->subject;
        $data['attachments'] = $criticalEmail->attachments;
       
        $res = $this->sendEmail($data);
        
        if (isset($res['message']) && $res['message'] == 'success') {
            $criticalEmail->update(['status'=>1,'sent_at'=>now()]);
           
            return back()->with('message', 'Email resent successfully');
        }else{
            $criticalEmail->update(['errors' => $res['error']]);

            return back()->with('error', $res['error']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CriticalEmail  $criticalEmail
     * @return \Illuminate\Http\Response
     */
    public function edit(CriticalEmail $criticalEmail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CriticalEmail  $criticalEmail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CriticalEmail $criticalEmail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CriticalEmail  $criticalEmail
     * @return \Illuminate\Http\Response
     */
    public function destroy(CriticalEmail $criticalEmail, $id)
    {
        $criticalEmail = CriticalEmail::find($id);
        $criticalEmail->delete();
        return back()->with('message', 'Delete Successful');
    }


}
