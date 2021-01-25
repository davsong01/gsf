<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use Paystack;
use App\Setting;
use App\TempUser;
use App\Mail\AdminMail;
use App\Mail\Welcomemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class PaymentController extends Controller
{

    public function redirectToGateway(Request $request)
    {
        $type = json_decode($request['metadata'], true);
        $existing_email = TempUser::whereEmail($request->email)->first();

        if(!$existing_email){
            //Create new details in temp users
            TempUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'type' => $type['type'],
                'chapter' => $request->chapter
            ]);
        }
        
        //Validate individual registration
        if(isset($type['type']) && $type['type'] == '1'){
            //check amount
            if($request->amount <> (Setting::select('registration_fee')->first()->value('registration_fee') * 100)){
                return redirect(url('/#register'))->with('error', 'Invalid amount');
            }
        }

        //Validate Fellowship registration
        if(isset($type['type']) && $type['type'] == '2'){
            
            //check amount
            if($request->amount <> (Setting::select('registration_fee')->first()->value('registration_fee') * $request->participants * 100)){
                return redirect(url('/#register'))->with('error', 'Invalid amount');
            }
        }

        //Validate Alumni Registration
        if(isset($type['type']) && $type['type'] == '3'){
            
            //check amount
            if($request->amount <> (Setting::select('alumni_fee')->first()->value('alumni_fee') * 100)){
                return redirect(url('/#register'))->with('error', 'Invalid amount');
            }
        }

        try{
            return Paystack::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            return redirect(url('/#register'))->with('error','Transaction token has expired or details not correct. Please refresh the page and try again');
        }        
    }

    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
      
        if($paymentDetails['data']['status'] === 'success'){
            //get participant details
            $participant = TempUser::whereEmail($paymentDetails['data']['customer']['email'])->first();
            $data['name'] = $participant->name;
            $data['phone'] = $participant->phone;
            $data['type'] = $paymentDetails['data']['metadata']['type'];
            $data['email'] = $paymentDetails['data']['customer']['email'];
            $password = bcrypt($participant->phone);
            $data['amount'] = $paymentDetails['data']['amount']/100;
            $data['transid'] = $paymentDetails['data']['reference'];
            $data['payment_type'] = "PAYSTACK";
            $data['chapter'] = $participant->chapter;
            

            if(isset($paymentDetails['data']['metadata']['type']) && $paymentDetails['data']['metadata']['type'] == '1'){
                $data['slot'] = 1;
                $ledge = 'AOP';
                $data['level'] = 'Participant';
                $data['slot_filled'] = 1;
            }

            if(isset($paymentDetails['data']['metadata']['type']) && $paymentDetails['data']['metadata']['type'] == '2'){
                $data['slot'] = $data['amount'] / Setting::select('registration_fee')->first()->value('registration_fee');
                 $ledge = 'AOP';
                 $data['level'] = 'Moderator';
                 $data['slot_filled'] = 1;
            }

            if(isset($paymentDetails['data']['metadata']['type']) && $paymentDetails['data']['metadata']['type'] == '3'){
                $data['slot'] = 1;
                $ledge = 'AOA';
                $data['level'] = 'Alumni';
                $data['slot_filled'] = 1;
            }


            try{
            // Create new user
            $user = User::Create([
                'name' => $data['name'],
                'slot' => $data['slot'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'chapter' => $data['chapter'],
                'type' => $data['type'],
                'slot_filled' => isset($data['slot_filled']) ? $data['slot_filled'] : 0,
                'level' => $data['level'],
                'amount_paid' => $data['amount'],
                'password' => $password,
                'payment_type' => $data['payment_type'],
                'transid' => $data['transid'],
            ]);
         
            $user->update([
                 'conference_number' =>'GSF-'.$ledge.'-'.$user->id,
            ]);

            }catch (\Illuminate\Database\QueryException $ex) {
                return redirect(route('index'))->with('error', $ex);
            }            

            
            $data['conference_number'] = $user->conference_number;

            //delete temp user
            $participant->delete();
            
           
            //send email to participant
            Mail::to($data['email'])->send(new WelcomeMail($data));
           
            //send email to official email
            Mail::to(Setting::select('official_email')->first()->value('official_email'))->send(new AdminMail($data));
       
            //include thankyou page
            return view('thankyou', compact('data'));

        }else {
            dd('Transaction failed! We have not received any money from you.');
        }
     
    }     

    private function process($paymentDetails){
        
    }

    //set balance and determine user receipt values
    private function dosubscript1($balance){

        if($balance <= 0){
            return 'Full payment';
        }else return 'Part payment';
    }

    //return payment status
    private function paymentStatus($balance){
        if($balance <= 0){
            return 1;
        }else return 0;
    }

    //return message for if earlybird is not checked
    private function dosubscript2($balance){
        if($balance <= 0){
            return 'Earlybird payment';
        }else return 'Part payment';
    }
}