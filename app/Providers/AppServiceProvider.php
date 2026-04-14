<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('vendor-only', function ($user) {
            return $user->isVendor();
        });

        Gate::define('buyer-only', function ($user) {
            return $user->isBuyer();
        });
    }
}
