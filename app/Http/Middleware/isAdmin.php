<?php

namespace App\Http\Middleware;

use auth;
use Closure;
use App\Http\Controllers\Controller;

class isAdmin extends Controller
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
        if(!in_array(auth()->user()->role, $this->isAdminRole())){
            return redirect(url('/'));
        }

        return $next($request);
    }
}
