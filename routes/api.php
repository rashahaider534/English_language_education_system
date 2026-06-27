<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:2,1');
Route::post('/verifyOtp/{type}' , [AuthController::class, 'verifyOtp']);
Route::post('/resendOtp/{type}' , [AuthController::class, 'resendOtp'])
           ->middleware('throttle:3,1');
Route::post('/forgotPassword' , [AuthController::class, 'forgotPassword'])
           ->middleware('throttle:3,1');
Route::post('/resetPassword' , [AuthController::class, 'resetPassword'])
    ->middleware('throttle:3,1');
Route::post('/login' , [AuthController::class, 'login']);
Route::get('/google/redirect' , [SocialAuthController::class, 'redirect']);
Route::get('/google/callback' , [SocialAuthController::class, 'callback']);
Route::middleware(['auth:sanctum','role:student|teacher'])->group(function () {
    Route::post('/logout' , [AuthController::class, 'logout']);
});
