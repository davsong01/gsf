<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (env('APP_ENV') === 'local') {
            $path = "/Applications/MAMP/htdocs/gsf/public";
        } else {
            $path = '/home/shinliih/directory.gsfnational.org';
        }

        $this->app->bind('path.public', function () use ($path) {
            return $path;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        
    }
}
