<?php

namespace App\Http\Middleware;

use Closure;
use session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SwitchUser
{
    private const IMPERSONATOR_ID_SESSION_KEY = 'impersonator_id';

    public function handle($request, Closure $next)
    {
      
        if(Auth::user()->level == 'Admin'){
 
            if(
                $request->session()->has(self::IMPERSONATOR_ID_SESSION_KEY)
                || $request->session()->has('switchuser')
            )
            {
               $d = Auth::onceUsingId(
                   $request->session()->get(self::IMPERSONATOR_ID_SESSION_KEY)
                   ?? $request->session()->get('switchuser')
               );
              
            }
        }
      
        return $next($request);
    }
}
