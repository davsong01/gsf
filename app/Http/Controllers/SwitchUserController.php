<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Stakeholder;
use App\Models\SwitchUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SwitchUserController extends Controller
{
    public function index(Request $request, $id, $type = null)
    {
        if(Auth::user()->role <> 1)
        {
            return redirect('/');
        }
        $admin = Auth::user()->id;

        if ($type === 'stakeholder') {
            $stakeholder = Stakeholder::findOrFail($id);

            Session::put('switchuser', $admin);
            Session::put('switchuser_guard', 'stakeholder');

            Auth::guard('stakeholder')->loginUsingId($stakeholder->id);
            $stakeholder->update(['last_login' => now()]);

            return redirect()->route('stakeholders.dashboard');
        }

        $user = User::findOrFail($id);

        Session::put('switchuser', $admin);
        Session::put('switchuser_guard', 'web');

        Auth::user()->setSwitchingUser($admin);
        Auth::loginUsingId($user->id);

        if(in_array($user->conference_role, ['admin'])){
            return redirect()->route('conferencemanagement.index');
        }

        if(Auth::user()->role <> 1)
        {
            return redirect('/account');
        }

        return back()->with('error', 'Switch disabled for this user!');
    }

    public function stopSwitching(Request $request)
    {
        $admin = Session::pull('switchuser');
        $guard = Session::pull('switchuser_guard', 'web');

        if (!$admin) {
            return redirect()->route('users.index');
        }

        if ($guard === 'stakeholder') {
            Auth::guard('stakeholder')->logout();
        } else {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Auth::loginUsingId($admin);
        }

        $message = "Welcome back boss!";
        $smiley = '<b>These are bold texts</b>';
        return redirect(route('users.index'))->with('welcomeback', $message);   

    }
    
}
