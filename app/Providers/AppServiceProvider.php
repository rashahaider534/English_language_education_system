<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::enforceMorphMap([
            'course' => 'App\Models\Course',
            'lesson' => 'App\Models\Lesson',
            'level'  => 'App\Models\Level',
            'placement_test' => 'App\Models\PlacementTest',
            'user'   => 'App\Models\User','question' => 'App\Models\Question',
            'test'     => 'App\Models\Test',
            'role'       => 'Spatie\Permission\Models\Role',
            'permission' => 'Spatie\Permission\Models\Permission',
        ]);
    }
}
