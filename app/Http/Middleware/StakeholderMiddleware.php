<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class StakeholderMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Check if the stakeholder is authenticated under the stakeholder guard
        if (!Auth::guard('stakeholder')->check()) {
            // Redirect unauthenticated users to the stakeholder login page
            return redirect()->route('stakeholders.loginpage')
                ->with('error', 'Please login to access your dashboard.');
        }

        return $next($request);
    }
}
