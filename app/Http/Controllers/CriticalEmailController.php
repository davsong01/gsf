<?php

namespace App\Http\Controllers;

use App\CriticalEmail;
use Illuminate\Http\Request;

class CriticalEmailController extends Controller
{
    public function getContent($data=null){
        // $data = [
        //    'conference_edition_id'=>'dsddds',
        //    'name' => 'name',
        //    'email' => 'new@gmail.com',
        //    'phone' => '0000000',
        //    'family_id' => 'GHSF434434-P',
        //    'amount_paid' => 1500,
        //    'hostel' => 'SAINT PAUL',
        //    'foodstand' => 'FOODSTASND',
        //     'conference_edition_id' => 1,
        //     'category'=> 'welcome_mail'
        // ];
        switch ($data['type']) {
            case 'welcome_mail':
                $account = "<a href=". route('conferencemanagement.index', ['edition' => $data['conference_edition_id']]).">Login</a>";
                $content = "Dear ".$data['name']. ", <br><br>
                    Your registration for GSF National conference is successful. <br><br>Below are the details of your registration <br><br>
                    <strong>Name: </strong>".$data['name']."<br>
                    <strong>Email: </strong>". $data['email']."<br>
                    <strong>Phone: </strong>". $data['phone']."<br>
                    <strong>Amount Paid: </strong> &#8358;". $data['amount'] . "<br><br>
                    <strong>Allocation Details:</strong><br>";

                if(isset($data['hostel']) && !empty($data['hostel'])){
                    $content .= "<strong>Allocated Hostel: </strong>".$data['hostel']."<br>";
                }

                if (isset($data['foodstand']) && !empty($data['foodstand'])) {
                    $content .= "<strong>Allocated Foodstand: </strong>" . $data['foodstand'] . "<br><br>";
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
    public function index()
    {
        //
    }

    public function runEmailCron(){
        
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
        //
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
    public function destroy(CriticalEmail $criticalEmail)
    {
        //
    }


}
