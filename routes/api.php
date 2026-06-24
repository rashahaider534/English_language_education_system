<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:2,1');
Route::post('/verifyOtp' , [AuthController::class, 'verifyOtp']);
Route::post('/resendOtp' , [AuthController::class, 'resendOtp'])
    ->middleware('throttle:1,1');
Route::post('/login' , [AuthController::class, 'login']);
Route::middleware(['auth:sanctum','role:student|teacher'])->group(function () {
    Route::post('/logout' , [AuthController::class, 'logout']);
});
