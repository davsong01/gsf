<?php

namespace App\Http\Controllers;

use Auth;
use App\Enums\OtpTypeEnum;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ForgotPasswordService;

class StakeholderLoginController extends Controller
{
    public function showStakeholderLoginForm()
    {
        return view('auth.stakeholder.login', ['url' => 'stakeholder']);
    }


    public function logout(Request $request)
    {
        $request->session()->invalidate();
        return redirect(route('stakeholders.login'));

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

    public function showForgotPasswordForm()
    {
        return view('auth.stakeholder.forgot-password');
    }

    public function sendForgotPasswordLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:stakeholders,email',
        ]);
        if($request->user_type == 'stakeholder'){
            $user = Stakeholder::where('email', $request->email)->first();
        } else {
            return back()->withErrors(['email' => 'Invalid user type.']);
        }

        ForgotPasswordService::getOrCreateValidOtp($user, OtpTypeEnum::FORGOT_PASSWORD);
        $isSent = true;

        return back()->with('message', 'Enter the OTP sent to your email.', compact('isSent'));
    }

    public function verifyOtp($user_id, $type)
    {
        $user = VaUser::findOrFail($user_id);
        $type = OtpTypeEnum::from($type);

        if($type == OtpTypeEnum::SIGNUP_VERIFICATION){
            if ($user->email_verified_at) {
                return redirect()->route('va.login')
                    ->with('success', 'Your email is already verified.');
            }
        }

        $otp = OtpService::getOrCreateValidOtp($user, $type);

        return view('va.pages.otp_verification', [
            'user' => $user,
            'otp' => $otp,
            'expiresAt' => $otp->expires_at->timestamp,
            'title' => 'Verify Your Email',
            'text' => 'Enter the 6-digit OTP sent to your email to complete your registration.',
            'isExpired' => false,
        ]);
    }
}
