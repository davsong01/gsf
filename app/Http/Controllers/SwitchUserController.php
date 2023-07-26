<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\SwitchUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SwitchUserController extends Controller
{
    public function index(Request $request, $id)
    {
        if(Auth::user()->role <> 1)
        {
            return redirect('/');
        }
        $admin = Auth::user()->id;
        $user = User::find($id);
        
        Auth::user()->setSwitchingUser($admin);
        Auth::loginUsingId($user->id);

        if(in_array($user->conference_role, ['admin'])){
            return redirect(route('conferencemanagement.index',['edition'=>$this->edition]));
        }

        // Guard against administrator switch
        if(Auth::user()->role <> 1)
        {
            return redirect('/account');
        }
        else
        {
            return back()->with('error', 'Switch disabled for this user!');
        }
    }

    public function stopSwitching(Request $request)
    {
        
        // Auth::user()->stopSwitchingUser();
        $admin = \Session::get('switchuser');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::loginUsingId($admin);

        $message = "Welcome back boss!";
        $smiley = '<b>These are bold texts</b>';
        return redirect(route('users.index'))->with('welcomeback', $message);   

    }
    
}