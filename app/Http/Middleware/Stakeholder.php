<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class Stakeholder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!auth::guard('stakeholder')->check())return redirect(route('stakeholder.login'));

        if (!Auth::guard('stakeholder')->user()) {
            return redirect(route('stakeholder.login'));
        }

        return $next($request);
    }
}
