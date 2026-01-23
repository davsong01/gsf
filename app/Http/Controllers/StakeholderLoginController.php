<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StakeholderLoginController extends Controller
{
    public function showStakeholderLoginForm()
    {
        return view('auth.stakeholder.login', ['url' => 'stakeholder']);
    }

    public function stakeholderLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('stakeholder')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();

            $user = Auth::guard('stakeholder')->user();

            if ($user->status !== 'active') {
                Auth::guard('stakeholder')->logout();
                return $this->loginFailed('Account is inactive.');
            }

            if ($user->designation_id) {
                if (
                    !$user->designation ||
                    $user->designation->status !== 'active'
                ) {
                    Auth::guard('stakeholder')->logout();
                    return $this->loginFailed('Your designation is inactive.');
                }
            }

            $user->update([
                'last_login' => now(),
            ]);

            return redirect()->intended('/stakeholders/dashboard');
        }

        return $this->loginFailed();
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        return redirect(route('stakeholders.login'));

    }

    private function validator(Request $request)
    {
        //validation rules.
        $rules = [
            'email'    => 'required|email|exists:stakeholders|min:5|max:191',
            'password' => 'required|string|min:4|max:255',
        ];

        //custom validation error messages.
        $messages = [
            'email.exists' => 'These credentials do not match our records.',
        ];

        //validate the request.
        $request->validate($rules,$messages);
    }

    private function loginFailed($message=null){
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $message ?? 'Login failed, please try again with the right credentials!');
    }

}
