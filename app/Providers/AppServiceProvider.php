<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (
            request()->header('x-forwarded-proto') === 'https' ||
            str_contains(request()->getHost(), '.loca.lt') ||
            str_contains(request()->getHost(), '.trycloudflare.com') ||
            str_contains(request()->getHost(), '.railway.app') ||
            str_contains(request()->getHost(), '.onrender.com') ||
            str_contains(request()->getHost(), '.ngrok') ||
            str_starts_with((string) config('app.url'), 'https://')
        ) {
            URL::forceScheme('https');
        }
    }
}
