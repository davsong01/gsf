<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Donation;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function allDonations(Request $request){
        $count = 1;
        $donations = Donation::where('type', 'donation')->orWhere('type','annual-due')->get();
        
        return view('admin.donations.otherdonation', compact('donations', 'count'));
    }

    public function redirectToGateway(Request $request)
    {
        $setting = GeneralSetting::first();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'amount' => 'required',
            'campus' => 'nullable',
            'membership_status' => 'required'
        ]);

        $request['transid'] = $this->generateTransactionId();
        $request['currency'] = 'NGN';

        // Save donation
        Donation::create([
            "name" => $request->name,
            "amount" => $request->amount,
            "phone" => $request->phone,
            "email" => $request->email,
            "campus" => $request->campus,
            "type" => $request->type,
            "remarks" => $request->remarks,
            "membership_status" => $request->membership_status,
            'transid' => $request['transid']
        ]);


        // redirect to gateway
        try {
            $request['amount'] = $request['amount'] * 100;
            $callback = url('/') . '/payment/donation-callback';
            $url = app('App\Http\Controllers\PaymentController')->queryPaystack($request->all(), $setting, $callback);
           
            if (isset($url['error'])) {
                return back()->with('error', $url['error']);
            } elseif (isset($url) && !empty($url)) {
                return redirect()->away($url);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e . 'Transaction token has expired or details not correct. Please refresh the page and try again');
        }
    }
    
    public function handleDonationGatewayCallback(Request $request, $admin = "", $transfer_confirm = "", $onsite_confirm = ""){
        $reference = $request->reference;
        $setting = GeneralSetting::first();
        
        $paymentDetails = app('App\Http\Controllers\PaymentController')->verify($reference, $setting);
        
        if (isset($paymentDetails) && $paymentDetails->status === 'success') {
            //get participant details
            $donation = Donation::where('transid', $request->reference)->whereNull('status')->first();
            
            if (empty($donation)) {
                return redirect(route('newdonation'))->with('warning', 'Looks like you have previously made this transaction');
            }
            // Update donation status
            $donation->update(['status' => 'complete']);
            // Send email

            $data = $donation->toArray();
            $data['type'] = 'donation_thank_you_mail';

            // TODO!!! Create new user here
            // $data = array_merge($data, $this->prepareUserData($data));
            // $user = $this->createUser($data);
            // $payment = $this->createPayment($data, $user);
            // dd($user, $payment);

            //send email to participant
            $email = [
                'subject' => 'Thank you for your donation.',
                'recipient_name' => $data['name'],
                'recipient' => $data['email'],
                'type' => 'conference_bulk_email',
                'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
            ];
            
            $this->logEmail($email);
            // Mail::to($data['email'])->send(new WelcomeMail($data));
            $data['type'] = 'admin_donation_general_notification';
            $data['chapter'] = Chapter::find($donation->campus)->name;

            $email2 = [
                'subject' => 'New Donation',
                'type' => 'conference_bulk_email',
                'recipient' => $setting->official_email,
                'chapter' => $data['chapter'],
                'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
            ];
            
            //send email to official email
            $this->logEmail($email2);
            return redirect(route('newdonation'))->with('message', 'Thank you for this donation');
            
        } else {
            return redirect(route('newdonation'))->with('error', 'Looks like something went wrong');
        }
    
    }

    public function prepareUserData($data){
        $data['name'] = $data['name'] ?? null;
        $data['phone'] = $data['phone'] ?? null;
        $data['sex'] = $data['gender'] ?? null;
        $data['type'] = $data['type'] ?? null;
        $data['email'] = $data['email'] ?? null;
        $data['password'] = bcrypt($data['phone']);
        $data['amount'] = $data['amount'];
        $data['transid'] = $data['transid'];
        $data['payment_type'] = "PAYSTACK";
        $data['conference_edition_id'] = $data['conference_edition_id'];
        $data['chapter'] = $data['chapter_id'] ?? null;
        $data['field_id'] = $data['field_id'] ?? null;
        $data['remarks'] = $data['remarks'] ?? null;

        $type = $data['type'] ?? null;
        $extras = $this->getExtras($type, $setting, $data['amount']);

        $data['slot'] = $extras['slot'] ?? null;
        $ledge = $extras['ledge'] ?? null;
        $data['level'] = $extras['level'] ?? null;
        $data['slot_filled'] = $extras['slot_filled'] ?? null;

        return [];
    }

    public function createUserFromDonation($data){

    }

    public function index(Request $request, $edition="")
    {
        $edition = ConferenceEdition::find($request->edition);
        $count = 1;
        $donations = Donation::where('conference_edition_id',$edition->id)->get();
        
        return view('admin.donations.index', compact('donations', 'count', 'edition'));
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
     * @param  \App\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function show(Donation $donation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function edit(Donation $donation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Donation $donation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Donation $donation)
    {
        //
    }
}
