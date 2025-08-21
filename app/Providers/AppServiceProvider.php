<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('is-seller', function ($user) {
            return $user->role === 'seller';
        });

        Gate::define('is-client', function ($user) {
            return $user->role === 'client';
        });
        Gate::define('is-admin', function ($user) {
            return $user->role === 'admin';
        });
    }
}
