<?php

namespace App\Providers;

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
        Relation::enforceMorphMap([
            'course' => 'App\Models\Course',
            'lesson' => 'App\Models\Lesson',
            'level'  => 'App\Models\Level',
            'user'   => 'App\Models\User','question' => 'App\Models\Question',
            'test'     => 'App\Models\Test',
            'role'       => 'Spatie\Permission\Models\Role',
            'permission' => 'Spatie\Permission\Models\Permission',
        ]);
    }
}
