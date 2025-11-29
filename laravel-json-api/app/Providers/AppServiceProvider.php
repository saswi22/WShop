<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Schema;
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
        // Fix for older MySQL/MariaDB default index length issues when using utf8mb4
        // Ensures default string columns use a max length that fits older engines.
        Schema::defaultStringLength(191);

        Passport::enablePasswordGrant();
    }
}
