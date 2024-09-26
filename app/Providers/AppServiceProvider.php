<?php

namespace App\Providers;

use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\RedirectIfAuthenticatedAdmin;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app('router')->aliasMiddleware('guest.admin', RedirectIfAuthenticatedAdmin::class);
        app('router')->aliasMiddleware('check.admin', CheckAdminRole::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
