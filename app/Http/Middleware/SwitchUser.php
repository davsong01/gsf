<?php

namespace App\Http\Middleware;

use Closure;
use session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SwitchUser
{
    public function handle($request, Closure $next)
    {
       
        if(Auth::user()->permission == 2){
            if($request->session()->has('switchuser'))
            {
               $d = Auth::onceUsingId($request->session()->get('switchuser'));
            }
        }
      
        return $next($request);
    }
}
