<?php

namespace App\Http\Controllers\Auth;

use App\ConferenceEdition;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';
    private $conference;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->conference = ConferenceEdition::where('status', 'active')->where('close_registration', '>', date('Y-m-d'))->first();
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        if ($this->conference) {
           
            return view('frontend.conference.template' . $this->conference->template_id . '.passwords.reset')
                ->with('conference', $this->conference)
                ->with(['token' => $token, 'email' => $request->email]);
        } else {
            return view('auth.passwords.reset')->with(
                ['token' => $token, 'email' => $request->email]
            );
        }
    }
}
