<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Email;
use App\Models\Chapter;
use App\Jobs\sendMails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserEmailsController extends Controller
{
    public function index()
    {
        $emails = Email::whereType(1)->get();
        $count = 1;
        return view('admin.emails.index', compact('emails', 'count'));
    }

    public function create()
    {
        $chapters = Chapter::all();
        return view('admin.emails.create', compact('chapters'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'recipient' => 'required',
            'chapter_id' => 'required',
            'subject' => 'required | min: 3',
            'content' => 'required | min: 10'
        ]);
        
        $recipients = User::Wherehas('campus')->where('role','<>', 1);
        
        //Determine Chapter 
        if($data['chapter_id'] <> 'All'){
            $recipients = $recipients->whereChapterId($data['chapter_id']);
        }else{
            $recipients = $recipients;
        }

        // Determine recipient type
        if($data['recipient'] == '1'){
            $recipients = $recipients->whereStatus(1);
        }elseif($data['recipient'] == '0'){
            $recipients = $recipients->whereStatus(0);
        }else{
            $recipients = $recipients;
        }

        $data['type'] = 'email';
        $recipients = $recipients->get();

        $email = Email::create([
            'sender' => Auth::user()->name,
            'recipient' => $data['recipient'],
            'subject' => $data['subject'],
            'content' => $data['content'],
            'count' => $recipients->count(),
            'type' => 0
        ]);
        
        $data['type'] = 0;
   
        $mail = new sendMails($data, $recipients);
        dispatch($mail);

        return back()->with('message', count($recipients). " emails were successfully sent!");
    }

}
