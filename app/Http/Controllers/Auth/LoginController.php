<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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
    private $frontend;



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->conference = activeConferenceEdition();
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
        // dd(Hash::make('12345678'));
        return view('frontend.conference.template' . $this->conference->template_id . '.login')
          ->with('setting', $this->conference);
      } else {
        return view('frontend.' . frontendTemplate() . '.login');

        // return view('auth.login');
      }
    
    }

}
