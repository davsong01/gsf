<?php

namespace App\Http\Controllers\Auth;

use App\ConferenceEdition;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
   

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account';
    protected $conference;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->conference = ConferenceEdition::where('status', 'active')->where('close_registration', '>', date('Y-m-d'))->first();
      
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
      return 'family_id';
    }

    public function showLoginForm()
    {
      if ($this->conference) {
        $setting = $this->conference;
        $conference_year = Carbon::parse($setting->start_date)->year;
        
        return view('frontend.conference.template' . $this->conference->template_id . '.login')
          ->with('conference', $this->conference);
      } else {
        return view('auth.login');
      }
    
    }

}
