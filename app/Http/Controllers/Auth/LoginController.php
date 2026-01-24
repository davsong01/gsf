<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    public function login(Request $request)
    {
        $request->validate([
            'family_id' => 'required|string',
            'password'  => 'required|string',
        ]);

        $login = $request->family_id;
        $password = $request->password;

        // Find user by email OR family_id
        $user = User::where('email', $login)
            ->orWhere('family_id', $login)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'login' => 'Invalid login credentials.',
            ])->withInput();
        }

        // Check if account is inactive
        if ($user->status == 0) {
            return back()->withErrors([
                'login' => 'Your account is inactive. Please contact the administrator.',
            ])->withInput();
        }

        // Check password
        if (!Hash::check($password, $user->password)) {
            return back()->withErrors([
                'password' => 'Incorrect password.',
            ])->withInput();
        }

        // Login user
        Auth::login($user, $request->filled('remember'));

        // Update last login timestamp
        $user->update(['last_login' => now()]);

        // Regenerate session
        $request->session()->regenerate();

        return redirect()->intended($this->redirectTo);
    }
}
