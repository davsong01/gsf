<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chapter;
use App\Models\Setting;
use App\Models\Moderator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ModeratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $count = 1;
        if(auth()->user()->level == 'Admin'){
            $participants = User::with(['hostel', 'moderator' ])->whereLevel('Moderator')->orderBy('created_at', 'desc')->get();
            
            foreach($participants as $participant){
                $participant['slots_filled'] = $participant->whereUploadedBy($participant->id)->count();
            }
            return view('admin.moderator.index', compact('participants', 'count'));
        }return abort(404);
        
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        if(auth()->user()->level == 'Admin'){

            $user = User::findorFail($id);
            $chapters = Chapter::orderBy('name')->get();
            
            return view('admin.moderator.edit', compact('user', 'chapters'));
       }
       return abort(404);
    }

    public function update(Request $request, Moderator $moderator)
    {
      
        if($request->amount_paid / $this->conferenceEdition()->registration_fee <> $request->slot){
            return back ()->with('error', 'Amount paid must correspond with number of slot for this moderator!');
        }
         //handle password
        if($request['password']){
            $request->password = Hash::make($request['password']);
        }else $request->password = $moderator->password;

        // dd($request->all());
            $moderator->name = $request->name;
            $moderator->sex = $request->sex;
            $moderator->email = $request->email;
            $moderator->phone = $request->phone;
            $moderator->chapter = $request->chapter;
            $moderator->amount_paid = $request->amount_paid;
            $moderator->slot = $request->slot;
            $moderator->payment_type = $request->payment_type;
            $moderator->transid = $request->transid;
            $moderator->password = 

            $moderator->save();
            

        return back()->with('message', 'Update successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        if(auth()->user()->level == 'Admin'){
            $user= User::findOrFail($id);

            $user->delete();

            return back()->with('message', 'Record has been deleted forever!');
        }
    }
}
