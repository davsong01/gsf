<?php

namespace App\Http\Controllers;

use App\User;
use App\Setting;
use App\Moderator;
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(auth()->user()->level == 'Admin'){

            $user = User::findorFail($id);

            return view('admin.moderator.edit', compact('user'));
       }
       return abort(404);
    }

    public function update(Request $request, Moderator $moderator)
    {
       
        if($request->amount_paid / Setting::select('registration_fee')->first()->value('registration_fee') <> $request->slot){
            return back ()->with('error', 'Amount paid must correspond with number of slot for this moderator!');
        }
         //handle password
        if($request['password']){
            $request['password'] = Hash::make($request['password']);
        }else $request['password'] = $moderator->password;

        try{
            $moderator->update($request->all());
        }catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();        
                return back()->with('error', $ex);
            }

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
