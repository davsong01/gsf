<?php

namespace App\Providers;

use App\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        
    }

    public function boot()
    {
        Builder::defaultStringLength(191);
        
        // View::composer('*', function($view)
        // {
        //     if (Auth::check()){
        //     $notifications = Notification::where('user_id',Auth::id() )->orderBy('created_at', 'DESC')->take(10)->get();
            
        //     $unread = 0;
        //     $read = 0;
        //     View::share('notifications', $notifications);
        //     View::share('read', $read);
        //     View::share('unread', $unread);
            
        //     }
        // });
            
       
        $setting = Setting::first();
        if($setting){
            
            View::share('setting', $setting);
           
        }
    }
}
