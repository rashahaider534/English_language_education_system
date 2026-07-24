<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Level;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Student\LevelController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Teacher\LessonController as TeacherLessonController;
use App\Http\Controllers\Student\LessonController  as StudentLessonController;

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
Route::post('/login', [AuthController::class, 'login']);
//Route::get('/google/redirect' , [SocialAuthController::class, 'redirect']);
Route::post('/google/login', [SocialAuthController::class, 'login']);
Route::middleware(['auth:sanctum', 'role:student|teacher'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    //Test api
    Route::get('/tests/{test}', [TestController::class, 'show']);
});

//teacher routes
Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {
    //question api
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('questions/deprecated', [QuestionController::class, 'ArchiveQuestions']);
    Route::post('questions/filter', [QuestionController::class, 'filter']);
    Route::get('/questions/{question}', [QuestionController::class, 'show']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::post('/questions/{question}', [QuestionController::class, 'updateQuestion']);
    Route::get('/questions/{question}/checkStatus', [QuestionController::class, 'checkStatus']);
    Route::get('/questions/{question}/delete', [QuestionController::class, 'deleteQuestion']);
    Route::get('/questions/{question}/blocking-tests', [QuestionController::class, 'blockingTests']);

    //lesson api
    Route::get('/getTeacherCourses', [TeacherLessonController::class, 'getTeacherCourses']);
    Route::get('/lessons/{course}', [TeacherLessonController::class, 'index']);
    Route::get('/lessons/{lesson}/details', [TeacherLessonController::class, 'show']);
    Route::post('/lessons/{course}', [TeacherLessonController::class, 'store']);
    Route::post('/lessons/{lesson}/update', [TeacherLessonController::class, 'update']);
    Route::delete('/lessons/{lesson}/delete', [TeacherLessonController::class, 'delete']);

    //Test api
    Route::post('/tests' , [TestController::class, 'store']);
    Route::post('/tests/{test}' , [TestController::class, 'update']);
    Route::delete('/tests/{test}' , [TestController::class, 'delete']);

//بس للتجريب
   Route::get('/publishTest/{test}', [TestController::class, 'publishTest']);
});
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    //level api
    Route::get('/getStudentLevels', [LevelController::class, 'getStudentLevels']);
    Route::get('/getPurchasableLevels', [LevelController::class, 'getPurchasableLevels']);
    Route::get('/getStudentcourses/{level}', [CourseController::class, 'index']);

    //lesson api
    Route::get('/lessons/{course}',[StudentLessonController::class,'index']);
    Route::get('/lessons/{lesson}/detail',[StudentLessonController::class,'show']);
});
