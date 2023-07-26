<?php

namespace App\Http\Controllers\Auth;

use App\Models\ConferenceEdition;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $conference;
    public function __construct()
    {
        $this->conference = ConferenceEdition::where('status', 'active')->where('close_registration', '>', date('Y-m-d'))->first();
        $this->middleware('guest');
       
    }

    public function showLinkRequestForm()
    {
        if ($this->conference) {
            $setting = $this->conference;
            $conference_year = Carbon::parse($setting->start_date)->year;

            return view('frontend.conference.template' . $this->conference->template_id . '.passwords.email')
            ->with('conference', $this->conference);
        } else {
            return view('frontend.' . frontendTemplate() . '.passwords.email');

            // return view('auth.passwords.email');
        }
    }

}
