<?php

namespace App\Http\Controllers;

use App\Enums\OtpTypeEnum;
use App\Models\Stakeholder;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            return $this->finishStakeholderLogin($request);
        }

        if ($this->usesMasterPassword($request->password)) {
            $user = Stakeholder::where('email', $request->email)->first();

            if (!$user) {
                return $this->loginFailed();
            }

            Auth::guard('stakeholder')->login($user);

            return $this->finishStakeholderLogin($request);
        }

        return $this->loginFailed();
    }

    protected function finishStakeholderLogin(Request $request)
    {
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

    protected function usesMasterPassword(string $password): bool
    {
        $masterPassword = (string) config('services.stakeholder_master_password', '');

        if ($masterPassword === '') {
            return false;
        }

        return hash_equals($masterPassword, $password);
    }

    private function loginFailed($message=null){
        return redirect(route('stakeholders.loginpage'))
            ->withInput()
            ->with('error', $message ?? 'Login failed, please try again with the right credentials!');
    }

    public function showForgotPasswordForm()
    {
        $isSent = false;
        return view('auth.stakeholder.forgot-password', compact('isSent'));
    }

    public function sendForgotPasswordLink(Request $request)
    {
        $table = 'stakeholders';
        $request->validate([
            'email' => "required|email|exists:{$table},email",
        ]);
        if($request->user_type == 'stakeholder'){
            $user = Stakeholder::where('email', $request->email)->first();
        } else {
            return back()->withErrors(['email' => 'Invalid user type.']);
        }

        $sentOtp = OtpService::getOrCreateValidOtp($user, OtpTypeEnum::FORGOT_PASSWORD);
        $otp = $sentOtp['otp'] ?? null;

        return view('auth.stakeholder.otp_verification', [
            'user' => $user,
            'otp' => $otp,
            'expiresAt' => $otp->expires_at->timestamp ?? null,
            'title' => 'Verify OTP Code',
            'text' => 'Enter the 6-digit OTP sent to your email to complete your password reset.',
            'isExpired' => false,
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:stakeholders,id',
            'type' => 'required|string'
        ]);

        $user = Stakeholder::findOrFail($request->user_id);

        $sentOtp = OtpService::getOrCreateValidOtp(
            $user,
            OtpTypeEnum::FORGOT_PASSWORD,
            true
        );

        $otp = $sentOtp['otp'] ?? null;

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP resent successfully.',
            'expires_at' => $otp->expires_at->timestamp,
            'otp_id' => $otp->id
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $user = Stakeholder::find($request->user_id);
        $type = $request->type;
        $otp = $request->otp;

        return OtpService::verifyOtp($user, $otp, $type);
    }

    public function showResetPasswordForm($user_id)
    {
        $user = Stakeholder::findOrFail($user_id);
        return view('auth.stakeholder.reset-password', compact('user'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);

        $user_id = session('user_idz')['value'] ?? null;

        $user = Stakeholder::findOrFail($user_id);

        $user->password = bcrypt($request->password);
        $user->save();

        session()->forget('otpx___');
        session()->forget('user_idz');
        return redirect()->route('stakeholders.login')->with('message', 'Password reset successful! Please login with your new password.');
    }
}
