<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {  
        $chapters = Chapter::all();
        if(auth()->user()->level == 'Admin'){
            return view('admin.index');
        }elseif(auth()->user()->level == 'Participant'){
            return view('participant.index', compact('chapters'));
        }

    }

    public function update($id){
        $user = Auth::user();
        dd($user);
    }

     public function getcard($id)
    {  
        $user = User::find($id);
 ini_set('max_execution_time', 300);

        //generate pdf from receipt view
        $pdf = PDF::loadView('card.id', $user);
        // $pdf = PDF::loadView('pdf.invoice', $data);
        return $pdf->download( $user->name.'-id-card.pdf');
   
        return back()->with('message', 'I.D. Card hasbenn downloaded');

    }
}
