<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Email;
use App\Models\Payment;
use App\Jobs\sendMails;
use App\Models\ConferenceEdition;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $count = 1;
        $edition = ConferenceEdition::find($request->edition);
        $emails = Email::where('conference_edition_id',$edition->id)->orderBy('created_at','desc')->get();
       
        return view('conference_management.admin.emails.index', compact('emails', 'count','edition'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        
        return view('conference_management.admin.emails.create',compact('edition'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'recipient' => 'required',
            'subject' => 'required | min: 3',
            'content' => 'required | min: 10'
        ]);

        $recipients = Payment::join('users', 'payments.user_id', '=', 'users.id')
            ->where(['payments.conference_edition_id' => $request->edition])
            ->select('users.name', 'users.email', 'users.phone','payments.level')
            ->orderBy('payments.created_at', 'desc');
       
        if($data['recipient'] == 'All'){
            $recipients = $recipients->get();
        }

        if($data['recipient'] == 'Nec'){
            $recipients = $recipients->whereLevel('Nec')->get();
        }

        if($data['recipient'] == 'Moderators'){
            $recipients = $recipients->whereLevel('Mederator')->get();
        }

        if($data['recipient'] == 'Alumni'){
            $recipients = $recipients->whereLevel('Alumni')->get();
        }

        if($data['recipient'] == 'Officials'){
            $recipients = $recipients->whereLevel('Official')->get();
        }

        if ($data['recipient'] == 'Participants') {
            $recipients = $recipients->whereLevel('Participant')->get();
        }
       
        $data['type'] = 'email';
       
        $recipients = $recipients->toArray();

        Email::create([
            'sender' => Auth::user()->name,
            'recipient' => $data['recipient'],
            'subject' => $data['subject'],
            'content' => $data['content'],
            'conference_edition_id' => $request->edition,
            'count' => count($recipients),
        ]);

        foreach($recipients as $recipient){
            $email = [
                'subject' => $data['subject'],
                'recipient_name' => $recipient['name'],
                'recipient' => $recipient['email'],
                'type' => 'conference_bulk_email',
                'content' =>  $data['content'],
            ];

            $this->logEmail($email);
        }
        // sendEmails::dispatch($details);
        // $mail = new sendMails($data, $recipients);
        // dispatch($mail);
       
        return back()->with('message', count($recipients). " emails were successfully sent!");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Email  $email
     * @return \Illuminate\Http\Response
     */
    public function show(Email $email)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Email  $email
     * @return \Illuminate\Http\Response
     */
    public function edit(Email $email)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Email  $email
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Email $email)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Email  $email
     * @return \Illuminate\Http\Response
     */
    public function destroy(Email $email)
    {
        //
    }
}
