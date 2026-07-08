<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        View::composer('dashboard.layouts.app', function ($view) {
            if (array_key_exists('dashboardUser', $view->getData())) {
                return;
            }

            $view->with('dashboardUser', [
                'name' => optional(auth()->user())->name ?: 'Admin User',
                'email' => optional(auth()->user())->email ?: 'admin@example.com',
                'role' => optional(auth()->user())->getRoleNames()->first() ?: 'Platform Administrator',
            ]);
        });
    }
}
