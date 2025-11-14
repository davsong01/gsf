<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\User;
use App\Models\Alumni;
use App\Models\Hostel;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AlumniController extends Controller
{
    
    public function index()
    {
        $count = 1;
        if(auth()->user()->level == 'Admin'){
            $participants = User::with(['hostel', 'foodstand'])->whereLevel('Alumni')->orderBy('created_at', 'desc')->get();
        
            return view('admin.alumni.index', compact('participants', 'count'));
        }return abort(404);
    }


   
    public function edit($id)
    {

        if(auth()->user()->level == 'Admin'){

            $user = User::findorFail($id);

            if($user->gender == 'Female'){
               $hostels = Hostel::whereType('Female')->whereLevel('Alumni')->get();
           }

           if($user->gender == 'Male'){
               $hostels = Hostel::whereType('Male')->whereLevel('Alumni')->get();
           }
            $chapters = Chapter::all();
            $foods = Food::all();
            
            return view('admin.alumni.edit', compact('user', 'hostels', 'foods', 'chapters'));
       }
       return abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Alumni  $alumni
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alumni $alumni)
    {
        //
    }

    public function destroy(Alumni $alumni)
    {
        //
    }
}
