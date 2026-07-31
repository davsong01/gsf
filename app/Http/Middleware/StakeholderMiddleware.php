<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class StakeholderMiddleware
{
    private const IMPERSONATOR_GUARD_SESSION_KEY = 'impersonator_guard';

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if ($request->session()->get(self::IMPERSONATOR_GUARD_SESSION_KEY) === 'stakeholder') {
            Auth::shouldUse('stakeholder');
        }

        $guard = Auth::guard('stakeholder');

        // 1. Auth check
        if (!$guard->check()) {
            return redirect()
                ->route('stakeholders.loginpage')
                ->with('error', 'Please login to access your dashboard.');
        }

        $stakeholder = $guard->user();

        // 2. Designation check (only if assigned)
        if ($stakeholder->designation_id) {
            $designation = $stakeholder->designation;

            if (!$designation || $designation->status !== 'active') {
                $guard->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('stakeholders.loginpage')
                    ->with('error', 'Your designation is inactive. Please contact the administrator.');
            }
        }

        if ($stakeholder->status !== 'active') {
            $guard->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('stakeholders.loginpage')
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }
        
        return $next($request);
    }

}
