<?php

namespace App\Http\Controllers;

use auth;
use App\Models\Payout;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Mail;

class PayoutController extends Controller
{
    public function index()
    {
        $i = 1;
        if(auth()->user()->level == 'Participant'){

            return view('participant.payout', compact('i'));

        }

        if(auth()->user()->level == 'Admin'){

            $payouts = User::whereUserId(auth()->user()->id)->orderBy('created_at', 'DESC')->get();

            return view('user.payout', compact('i', 'payouts'));
        }

        if(auth()->user()->permission == 2){

            $payouts = Payout::with('user')->orderBy('created_at', 'DESC')->get();

            return view('admin.users.payout', compact('i', 'payouts'));
        }

        return abort('404');
    }

    public function create()
    {
        if(auth()->user()->permission == 1){
            $pending_payouts = Payout::whereUserId(auth()->user()->id)->whereStatus(0)->count();
            return view('admin.users.newpayout', compact('pending_payouts'));
        }

        return abort(404);
    }

    public function store(Request $request)
    {
        $min_amount = Setting::first()->value('min_payout_amount');
        $pending_payouts = Payout::whereUserId(auth()->user()->id)->whereStatus(0)->get();

        if($pending_payouts->count() > 0){
             return back()->with('error', 'You have a pending payout request, please allow it to be approved before you can request for another withdrawal');
        }
        if(auth()->user()->permission == 1){
            if($request->amount < $min_amount || $request->amount > auth()->user()->wallet){
                return back()->with('warning', 'You are trying to request for withdrawal outside your payment range');
            }

            if($request->amount < $min_amount || $request->amount > auth()->user()->wallet){
                return back()->with('error', 'You are trying to request for withdrawal outside your payment range');
            }

            Payout::create([
                'user_id' => auth()->user()->id,
                'amount_requested' => $request->amount,
            ]);
                
            //Sendmail to admin
            $settings = $this->conferenceEdition();
           
            $data = [
                'type' => 'payout_notification',
                'username' => auth()->user()->username,
                'amount' => $request->amount,
                'subject' => $settings->new_requestpayment_admin_notification_email_subject .' '.auth()->user()->username,
                'content' => $settings->new_requestpayment_admin_notification_email_content,
            ];
           
            $admin_email = Setting::first()->value('admin_email');
            Mail::to($admin_email)->send(new NotificationEmail($data));
           
           return redirect(route('payouts.index'))->with('message', 'Your request for payment has been sent and the status is PENDING');
        }else return abort(404);
        
    }

    public function show(Payout $payout)
    {
        if(auth()->user()->permission == 2){
            //Update User wallet
            $payout->user->wallet -= $payout->amount_requested;
            $payout->user->save();
    
            //Update Payout status
            $payout->status = 1;
            $payout->amount_paid = $payout->amount_requested;
            $payout->paid_at = now();
            $payout->save();

            //Send Email to user
            $settings = $this->conferenceEdition();
            $data = [
                'type' => 'payment_made',
                'name' => $payout->user->username,
                'amount' => $payout->amount_paid,
                'wallet' => $payout->user->wallet,
                'subject' => $settings->new_payment_user_notification_email_subject,
                'content' => $settings->new_payment_user_notification_email_content
            ];
            Mail::to($payout->user->email)->send(new NotificationEmail($data));

            return back()->with('message', 'Update Successful, user has been notified via email');

        }else return abort(404);
    }

    public function edit(Payout $payout)
    {
        //
    }

    public function update(Request $request, Payout $payout)
    {
        
    }

    public function restore(Request $request, Payout $payout, $id)
    {
        if(auth()->user()->permission == 2){
            $payout = Payout::whereId($id)->first();
            
            $payout->user->wallet += $payout->amount_requested;
            $payout->user->save();

            $payout->delete();

            return back()->with('message', 'Payout has been deleted and user wallet updated');
        }else abort(404);
       
    }

    public function destroy(Payout $payout)
    {
        //
    }
}
