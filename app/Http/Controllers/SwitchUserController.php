<?php

namespace App\Http\Controllers;
use App\User;
use App\SwitchUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SwitchUserController extends Controller
{
    public function index(Request $request, $id)
    {
     
        if(Auth::user()->level <> 'Admin')
        {
            return redirect('/');
        }

        $user = User::find($id);

        Auth::user()->setSwitchingUser($user->id);

        
        // Guard against administrator switch
        if($user->level <> 'Admin')
        {
            return redirect('/account');
        }
        else
        {
            return back()->with('error', 'Switch disabled for this user!');
        }

    }

    public function stopSwitching()
    {
        
        Auth::user()->stopSwitchingUser();
        
        $message = "Welcome back boss!";
        $smiley = '<b>These are bold texts</b>';
        return redirect(route('users.index'))->with('welcomeback', $message);   

    }
    
}