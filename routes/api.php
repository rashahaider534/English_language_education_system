<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Level;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Student\LevelController;
use App\Http\Controllers\Student\CourseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:2,1');
Route::post('/verifyOtp/{type}', [AuthController::class, 'verifyOtp']);
Route::post('/resendOtp/{type}', [AuthController::class, 'resendOtp'])
    ->middleware('throttle:3,1');
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword'])
    ->middleware('throttle:3,1');
Route::post('/resetPassword', [AuthController::class, 'resetPassword'])
    ->middleware('throttle:3,1');
Route::post('/login' , [AuthController::class, 'login']);
//Route::get('/google/redirect' , [SocialAuthController::class, 'redirect']);
Route::post('/google/login' , [SocialAuthController::class, 'login']);
Route::middleware(['auth:sanctum','role:student|teacher'])->group(function () {
    Route::post('/logout' , [AuthController::class, 'logout']);
    Route::post('/createlevel',[LevelController::class,'store']);
});

//teacher routes
Route::middleware(['auth:sanctum','role:teacher'])->group(function () {
    Route::get('/questions' , [QuestionController::class, 'index']);
    Route::get('/questions/{question}' , [QuestionController::class, 'show']);
    Route::post('/questions' , [QuestionController::class, 'store']);
    Route::post('/questions/{question}' , [QuestionController::class, 'updateQuestion']);
    Route::get('/questions/{question}/checkStatus' , [QuestionController::class, 'checkStatus']);
    Route::get('/questions/{question}/delete' , [QuestionController::class, 'deleteQuestion']);
});
Route::middleware(['auth:sanctum','role:student'])->group(function () {
Route::get('/getStudentLevels',[LevelController::class,'getStudentLevels']);
Route::get('/getPurchasableLevels',[LevelController::class,'getPurchasableLevels']);
Route::get('/getStudentcourses/{level}',[CourseController::class,'index']);
});
