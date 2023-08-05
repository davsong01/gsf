<?php

namespace App\Http\Middleware;

use auth;
use Closure;

class isAdmin
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
        if (auth()->user()->permission == 1) {
            return redirect(url('/'));
        }

        return $next($request);
    }
}
