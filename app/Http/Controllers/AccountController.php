<?php

namespace App\Http\Controllers;

use App\Chapter;
use Illuminate\Http\Request;
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
}
