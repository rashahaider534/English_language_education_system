<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ChatSessionController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\ContentReviewController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\Teacher\TeacherStatsController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Admin\TestController as AdminTestController;
use App\Http\Controllers\UserAttemptController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Student\LevelController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Teacher\LessonController as TeacherLessonController;
use App\Http\Controllers\Student\LessonController  as StudentLessonController;
use App\Http\Controllers\Teacher\WordController as TeacherWordController;
use App\Http\Controllers\Student\WordController as StudentWordController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FirebaseTokenController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Student\LevelExceptionController;
use App\Http\Controllers\Student\RateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Student\PodcastController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:2,1');
Route::post('/verifyOtp/{type}', [AuthController::class, 'verifyOtp']);
Route::post('/resendOtp/{type}', [AuthController::class, 'resendOtp'])
    ->middleware('throttle:3,1');
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword'])
    ->middleware('throttle:3,1');
Route::post('/login', [AuthController::class, 'login']);
//Route::get('/google/redirect' , [SocialAuthController::class, 'redirect']);
Route::post('/google/login', [SocialAuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:student|teacher'])->group(function () {
    Route::post('/resetPassword', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:3,1');
    Route::post('/logout', [AuthController::class, 'logout']);
    //Test api
    Route::get('/tests/{test}', [TestController::class, 'show']);
    //comment api
    Route::post('/comments/{lesson}',[CommentController::class,'create']);
    Route::post('/comments/{comment}/update',[CommentController::class,'update']);
    Route::delete('/comments/{comment}/delete',[CommentController::class,'delete']);

    //notification
    Route::post('/firebase/token',[FirebaseTokenController::class, 'store']);
    Route::get('/notifications',[NotificationController::class,'getNotifications']);
    Route::get('/notifications/unread',[NotificationController::class,'getUnreadNotifications']);
    Route::get('/notifications/unreadcount',[NotificationController::class,'getUnreadCount']);
    Route::patch('/notifications/{notification}/markAsRead',[NotificationController::class,'markAsRead']);
    Route::patch('/notifications/markAllAsRead',[NotificationController::class,'markAllAsRead']);
    Route::delete('/notifications/{notification}/delete',[NotificationController::class,'deleteNotification']);
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
    Route::get('/lessons/{course}/teacher', [TeacherLessonController::class, 'index']);
    Route::get('/lessons/{lesson}/details', [TeacherLessonController::class, 'show']);
    Route::post('/lessons/{course}', [TeacherLessonController::class, 'store']);
    Route::post('/lessons/{lesson}/update', [TeacherLessonController::class, 'update']);
    Route::delete('/lessons/{lesson}/delete', [TeacherLessonController::class, 'delete']);

    //Test api
    Route::get('/tests' , [TestController::class, 'index']);
    Route::post('/tests' , [TestController::class, 'store']);
    Route::post('/tests/{test}' , [TestController::class, 'update']);
    Route::delete('/tests/{test}' , [TestController::class, 'delete']);

    //word api
    Route::post('/words/{lesson}/create',[TeacherWordController::class,'create']);
    Route::post('/words/{word}/update',[TeacherWordController::class,'update']);
    Route::delete('/words/{word}/delete',[TeacherWordController::class,'delete']);

    //Profile api
    Route::get('/teacher/profile', [TeacherProfileController::class , 'show']);
    Route::post('/teacher/profile', [TeacherProfileController::class , 'update']);

    //Submit content api
    Route::post('lessons/{lesson}/submit', [ContentReviewController::class, 'submitLesson']);
    Route::post('lessons/{lesson}/resubmit', [ContentReviewController::class, 'resubmitLesson']);
    Route::post('tests/{test}/submit', [ContentReviewController::class, 'submitTest']);
    Route::post('tests/{test}/resubmit', [ContentReviewController::class, 'resubmitTest']);

    Route::get('lessons/{lesson}/history', [ContentReviewController::class, 'lessonReviewHistory']);
    Route::get('tests/{test}/history', [ContentReviewController::class, 'testReviewHistory']);

    //Stats api
    Route::get('courses/{course}/stats', [TeacherStatsController::class, 'courseStats']);
    Route::get('tests/{test}/stats', [TeacherStatsController::class, 'testStats']);
});

Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    //level api
    Route::get('/getStudentLevels', [LevelController::class, 'getStudentLevels']);
    Route::get('/getPurchasableLevels', [LevelController::class, 'getPurchasableLevels']);
    Route::get('/getStudentcourses/{level}', [CourseController::class, 'index']);
    Route::get('/placement-test/status', [LevelController::class, 'getStatus']);

    //lesson api
    Route::get('/lessons/{course}',[StudentLessonController::class,'index']);
    Route::get('/lessons/{lesson}/detail',[StudentLessonController::class,'show']);

    //level exception
    Route::get('/levelexceptions/{status?}',[LevelExceptionController::class,'index']);
    Route::get('/levelexceptions/{levelException}/details',[LevelExceptionController::class,'view']);
    Route::post('/levelexceptions/{level}/create',[LevelExceptionController::class,'create']);
    Route::post('/levelexceptions/{levelException}/update',[LevelExceptionController::class,'update']);
    Route::delete('/levelexceptions/{levelException}/delete',[LevelExceptionController::class,'delete']);
    Route::delete('/level-exceptions/{levelException}/attachments/{media}',[LevelExceptionController::class, 'destroyAttachment']);

    //rate api
    Route::post('/rate/{course}',[RateController::class,'rate']);
    Route::delete('/rate/{rate}/delete',[RateController::class,'delete']);

    //word api
    Route::get('/words/{lesson}/lesson',[StudentWordController::class,'getLessonWords']);
    Route::get('/words_bank/know',[StudentWordController::class,'knownWords']);
    Route::get('/words_bank/learning',[StudentWordController::class,'learningWords']);
    Route::post('/words/{word}/know',[StudentWordController::class,'know']);
    Route::post('/words/{word}/learning',[StudentWordController::class,'learning']);
    Route::get('/words/quiz',[StudentWordController::class,'quizWords']);
    Route::post('/words/{word}/quiz_check',[StudentWordController::class,'checkAnswer']);

    //payment api
    Route::post('/payments/{level}/create-intent',[PaymentController::class,'createIntent']);
    Route::get('/payments/{paymentIntentId}/status',[PaymentController::class,'status']);

    //Podcast api
    Route::get('/podcasts/topics',[PodcastController::class,'getTopics']);
    Route::get('/podcasts/{topic}',[PodcastController::class,'getPodcastsByTopic']);
    Route::get('/podcasts/{podcast}/details',[PodcastController::class,'showDetail']);
    Route::post('/podcasts/{podcast}/open',[PodcastController::class,'openPodcast']);

    //Attempt api
    Route::post('/tests/{test}/start', [UserAttemptController::class, 'startAndShow']);
    Route::get('/startPlacementTest', [UserAttemptController::class, 'startPlacementTest']);
    Route::prefix('attempts/{attempt}')->group(function () {
        Route::post('/questions/{question}/submit-answer', [UserAttemptController::class, 'submitAnswer']);

        Route::post('/finish', [UserAttemptController::class, 'finish']);

        Route::post('/leave', [UserAttemptController::class, 'leave']);

        Route::get('/review', [UserAttemptController::class, 'review']);
    });

    //Profile api
    Route::get('/student/profile', [StudentProfileController::class , 'show']);
    Route::post('/student/profile', [StudentProfileController::class , 'update']);
    Route::get('/student/weeklyActivity' ,[StudentProfileController::class , 'weeklyActivity'] );

    //chat api
    Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
        Route::get('/sessions/active', [ChatSessionController::class, 'active']);
        Route::get('/sessions/history', [ChatSessionController::class, 'history']);
        Route::post('/sessions', [ChatSessionController::class, 'store']);
        Route::post('/sessions/{session}/messages', [ChatSessionController::class, 'sendMessage']);
        Route::post('/sessions/{session}/end', [ChatSessionController::class, 'end']);
        Route::get('/sessions/{session}', [ChatSessionController::class, 'showHistorySession']);
        Route::get('/topics', [ChatSessionController::class, 'availableTopics']);
    });

    //progress api
    Route::get('/courses/{course}/progress', [ProgressController::class, 'courseProgress']);
    Route::get('/levels/{level}/progress', [ProgressController::class, 'levelProgress']);

    //certificate api
    Route::get('/user-levels/{userLevel}/certificate', [CertificateController::class, 'getCertificate']);
    Route::get('/certificates', [CertificateController::class, 'index']);

    //contact us
    Route::post('/contact-us', [ContactUsController::class, 'store']);
});

//Route::post('generateLevelTest', [AdminTestController::class, 'generateLevelTest']);
//Route::post('updateTest/{test}', [AdminTestController::class, 'update']);
//Route::post(
//    '/admin/questions/placement/filter',
//    [AdminTestController::class, 'filterPlacementQuestions']
//);
//
//Route::post(
//    '/admin/levels/{level}/questions/filter',
//    [AdminTestController::class, 'filterLevelTestQuestions']
//);


