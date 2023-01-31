<?php

namespace App\Providers;

use App\ConferenceEdition;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $setting = ConferenceEdition::where('status','active')->first();
        if($setting){
            
            View::share('setting', $setting);
           
        }
    }
}
