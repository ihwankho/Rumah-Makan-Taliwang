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
        if (config('app.env') === 'production' || env('APP_URL') !== 'http://localhost') {
            URL::forceScheme('https');

            // Mengambil base URL langsung dari APP_URL di .env Anda
            URL::forceRootUrl(config('app.url'));
        }
    }
}
