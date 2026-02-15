<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\OtpTypeEnum;
use Illuminate\Support\Facades\Auth;

class OtpMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $otpSession = session('otpx___');

        // Check if session exists and not expired
        if ($otpSession['value'] ?? false) {
            if (isset($otpSession['expires_at']) && now()->lte($otpSession['expires_at'])) {
                // Still valid, continue request
                session()->forget('otpx___'); // optional: remove after first use
                return $next($request);
            }

            // Expired: clear session
            session()->forget('otpx___');
            session()->forget('user_idz');
        }

        // Handle redirect based on OTP type
        $type = $request->input('type');

        if (empty($type)) {
            return redirect('/login');
        }

        if ($type === OtpTypeEnum::FORGOT_PASSWORD->value) {
            return redirect()->route('stakeholders.login');
        }

        return redirect('/login');
    }
}
