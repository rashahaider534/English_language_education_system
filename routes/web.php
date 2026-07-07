<?php

use App\Http\Controllers\DashboardTemplateController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


    Route::get('/dashboard', [DashboardTemplateController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/users', [DashboardTemplateController::class, 'users'])->name('dashboard.users');
    Route::get('/dashboard/roles', [DashboardTemplateController::class, 'roles'])->name('dashboard.roles');
    Route::get('/dashboard/reports', [DashboardTemplateController::class, 'reports'])->name('dashboard.reports');
    Route::get('/dashboard/tables', [DashboardTemplateController::class, 'tables'])->name('dashboard.tables');
    Route::get('/dashboard/forms', [DashboardTemplateController::class, 'forms'])->name('dashboard.forms');
    Route::get('/dashboard/cards', [DashboardTemplateController::class, 'cards'])->name('dashboard.cards');
    Route::get('/dashboard/charts', [DashboardTemplateController::class, 'charts'])->name('dashboard.charts');
    Route::get('/dashboard/notifications', [DashboardTemplateController::class, 'notifications'])->name('dashboard.notifications');
    Route::get('/dashboard/profile', [DashboardTemplateController::class, 'profile'])->name('dashboard.profile');
    Route::get('/dashboard/settings', [DashboardTemplateController::class, 'settings'])->name('dashboard.settings');
    Route::get('/dashboard/blank', [DashboardTemplateController::class, 'blank'])->name('dashboard.blank');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
