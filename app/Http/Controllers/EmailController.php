<?php

namespace App\Http\Controllers;

use App\User;
use App\Email;
use App\Jobs\sendMails;
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
    public function index()
    {
        $emails = Email::all();
        $count = 1;
        return view('conference_management.admin.emails.index', compact('emails', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('conference_management.admin.emails.create');
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
     
        $recipients = User::where('role', '<>', 1)->select('name', 'email');

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
       
        $data['type'] = 'email';
        $recipients = $recipients->toArray();

        Email::create([
            'sender' => Auth::user()->name,
            'recipient' => $data['recipient'],
            'subject' => $data['subject'],
            'content' => $data['content'],
            'count' => count($recipients),
        ]);

        // sendEmails::dispatch($details);
        $mail = new sendMails($data, $recipients);
        dispatch($mail);

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
