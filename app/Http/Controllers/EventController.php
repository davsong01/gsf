<?php

namespace App\Http\Controllers;

use App\User;
use App\Event;
use App\Chapter;
use App\Jobs\sendMails;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
			if(auth()->user()->isAdmin()){
				$events = Event::orderBy('id', 'DESC')->get();
			}else $events = Event::whereChapterId(auth()->user()->chapter_id)->orderBy('created_at', 'DESC')->get();			
		}

        $count = 1;
        return view('admin.events.index', compact('events', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $chapters = Chapter::all();
        return view('admin.events.create', compact('chapters'));
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
            'title' => 'required',
            'venue' => 'required',
            'date' => 'required',
            'time' => 'required',
            'banners' => 'image|mimes:jpeg,png,jpg|max:2048',
            'chapter_id' => 'nullable'
        ]);

        //Handle Banners
		if ($request['banners']) {
			$banners = $this->uploadImage($request->banners, 'eventbanners');
			$data['banners'] = $banners;		
		} 

        //Handle Chapter
        if(auth()->user()->role == 1){
            $data['chapter_id'] = $request->chapter_id;
        }else{
            $data['chapter_id'] = auth()->user()->chapter_id;
        }

        if($request->chapter_id <> 0){
            $chapter = Chapter::findorFail($request->chapter_id);
          
            if($chapter->events->count() > 4){
                return back()->with('error', 'You can only add 5 events per chapter');
            }
        }

        //Handle Send Email
        if($request->has('sendemail')){
            if($request->chapter_id == 0){
                $recipients = User::whereRole(2)->get();
            }else{
                $recipients = User::whereRole(2)->whereChapterId($request->chapter_id)->get();
            }

            $data['sender'] = 'Gofamint Student Fellowship';
            $data['subject'] = $data['title'];
            $data['content'] = "You are specially invited to our next big event, please find details below: <br> 
                Title: " . $data['title']
                . "<br>
                Venue: " . $data['venue']
                . "<br
                Date: " . $data['date']
                . "<br>
                Time: " . $data['time']
                . "<br><br><br>
                Kindly check flyer for details
                ";
            $data['type'] = 'Event';
            //Send emails to users
            $recipients = $recipients->toArray();
          
            // Todo Send emails
            // $mail = new sendMails($data, $recipients);
            // dispatch($mail);
        }
  
        $event = Event::Create([
            'chapter_id' => $data['chapter_id'],
            'slug' => Str::slug($data['title']),
            'title' => $data['title'],
            'venue' => $data['venue'],
            'date' => $data['date'],
            'time' => $data['time'],
            'banners' => $data['banners']
        ]);

        return redirect(route('events.index'))->with('message', 'Event created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        $chapters = Chapter::all();

        return view('admin.events.edit', compact('chapters'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
  
        if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){

			if(auth()->user()->isSubAdmin() && auth()->user()->isMember() && auth()->user()->chapter_id <> $event->chapter_id){ 
				return abort(404);
			}

            $this->deleteImage($event->banners);
            $event->delete();
    
            return redirect(route('events.index'))->with('message', 'Event successfully deleted');
		}
    }
}
