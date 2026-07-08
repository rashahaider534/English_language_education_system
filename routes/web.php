<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LevelController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/language/{locale}', function ($locale) {
    if (! in_array($locale, ['ar', 'en'])) {
        abort(404);
    }
    session()->put('locale', $locale);
    return back();
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/levels', [LevelController::class, 'index'])->name('levels.index');
    Route::get('/levels/create', [LevelController::class, 'create'])->name('levels.create');
    Route::post('/levels', [LevelController::class, 'store'])->name('levels.store');
    Route::get('/levels/{level}/edit', [LevelController::class, 'edit'])->name('levels.edit');
    Route::put('/levels/{level}', [LevelController::class, 'update'])->name('levels.update');
    Route::patch('/levels/{level}/archive', [LevelController::class, 'archive'])->name('levels.archive');
});
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/levels', [LevelController::class, 'index'])->name('levels.index');
    Route::get('/levels/create', [LevelController::class, 'create'])->name('levels.create');
    Route::post('/levels', [LevelController::class, 'store'])->name('levels.store');
    Route::get('/levels/{level}/edit', [LevelController::class, 'edit'])->name('levels.edit');
    Route::put('/levels/{level}', [LevelController::class, 'update'])->name('levels.update');
    Route::patch('/levels/{level}/archive', [LevelController::class, 'archive'])->name('levels.archive');
});

require __DIR__ . '/auth.php';
