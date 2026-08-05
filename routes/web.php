<?php

use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\DashboardTemplateController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\LevelExceptionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\PodcastController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
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
});

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

Route::middleware(['auth', 'role:super-admin'])->group(function () {
    //Level Exception route
    Route::get('/levelexceptions', [LevelExceptionController::class, 'index'])->name('levelException.index');
    Route::get('/levelexceptions/{levelException}/details', [LevelExceptionController::class, 'show'])->name('levelException.show');
    Route::patch('/levelexceptions/{levelException}/start', [LevelExceptionController::class, 'startReview'])->name('levelException.review');
    Route::patch('/levelexceptions/{levelException}/approve', [LevelExceptionController::class, 'approve'])->name('levelException.approve');
    Route::patch('/levelexceptions/{levelException}/reject', [LevelExceptionController::class, 'reject'])->name('levelException.reject');
});

Route::middleware(['auth', 'role:admin|super-admin'])->group(function () {
    //level route
    Route::get('/levels', [LevelController::class, 'index'])->name('levels.index');
    //course route
    Route::get('/courses/{level}', [CourseController::class, 'index'])->name('courses.index');
    //lesson route
    Route::get('/courses/{course}/lessons/{status?}', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/pending', [LessonController::class, 'pending'])->name('lessons.pending');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::patch('/lessons/{lesson}/archive', [LessonController::class, 'archive'])->name('lessons.archive');

    //comment route
    Route::delete('/comments/{comment}/destroy', [CommentController::class, 'admindelete']);
    Route::patch('/comments/{comment}/block', [CommentController::class, 'block']);

});

Route::middleware(['auth:web'])->group(function () {
    Route::middleware(['role:admin|super-admin', 'permission:manage_levels'])
        ->group(function () {
            //level route
            Route::get('/levels/create', [LevelController::class, 'create'])->name('levels.create');
            Route::post('/levels', [LevelController::class, 'store'])->name('levels.store');
            Route::get('/levels/{level}/edit', [LevelController::class, 'edit'])->name('levels.edit');
            Route::put('/levels/{level}', [LevelController::class, 'update'])->name('levels.update');
            Route::patch('/levels/{level}/archive', [LevelController::class, 'archive'])->name('levels.archive');
        });

    Route::middleware(['role:admin|super-admin', 'permission:manage_courses'])
        ->group(function () {
            Route::get('/courses/{level}/create', [CourseController::class, 'create'])->name('courses.create');
            Route::post('/courses/{level}', [CourseController::class, 'store'])->name('courses.store');
            Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
            Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
            Route::patch('/courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');
        });

    Route::middleware(['role:admin|super-admin', 'permission:manage_placement_questions'])->group(function () {

        // Placement Questions Bank
        Route::get(
            '/questions/placement',
            [QuestionController::class, 'indexPlacementQuestions']
        )->name('questions.placement.index');

        Route::get(
            'questions/{question}',
            [QuestionController::class, 'show']
        )->name('questions.show');
        Route::get(
            '/questions/placement/create',
            [QuestionController::class, 'createPlacementQuestion']
        )->name('questions.placement.create');

        Route::post(
            '/questions',
            [QuestionController::class, 'store']
        )->name('questions.store');

        Route::get(
            '/questions/{question}/edit',
            [QuestionController::class, 'edit']
        )->name('questions.edit');

        Route::put(
            '/questions/{question}',
            [QuestionController::class, 'update']
        )->name('questions.update');

        Route::delete(
            '/questions/{question}',
            [QuestionController::class, 'deleteQuestion']
        )->name('questions.delete');
    });

    Route::middleware(['role:admin|super-admin', 'permission:manage_placement_tests'])
        ->prefix('tests/placement')
        ->name('tests.placement.')
        ->group(function () {
            Route::get('/', [TestController::class, 'indexPlacementTests'])->name('placement.index');
            Route::get('/create', [TestController::class, 'createPlacementTest'])->name('placement.create');
            Route::post('/', [TestController::class, 'storePlacementTest'])->name('placement.store');
            Route::get('/{test}', [TestController::class, 'showPlacementTest'])->name('placement.show');
            Route::get('/{test}/edit', [TestController::class, 'editPlacementTest'])->name('placement.edit');
        });

    Route::middleware(['role:admin|super-admin', 'permission:manage_level_tests'])
        ->prefix('levels/{level}/tests')
        ->name('tests.level.')
        ->group(function () {
            // Questions available for a Level Test
            Route::get(
                '/questions',
                [QuestionController::class, 'indexLevelTestQuestions']
            )->name('questions.index');
            Route::get('/', [TestController::class, 'indexLevelTests'])->name('levelTest.index');
            Route::get('/create', [TestController::class, 'createLevelTest'])->name('levelTest.create');
            Route::post('/', [TestController::class, 'generateLevelTest'])->name('levelTest.generate');
            Route::get('/{test}', [TestController::class, 'showLevelTest'])->name('levelTest.show');
        });

    Route::middleware(['role:admin|super-admin', 'permission:manage_placement_tests|manage_level_tests'])
        ->post('/tests/{test}', [TestController::class, 'update'])
        ->name('tests.update');

    Route::middleware(['role:admin|super-admin', 'permission:manage_podcast'])
        ->group(function () {
            //topic route
            Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
            Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
            Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
            Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
            Route::patch('/topics/{topic}/update', [TopicController::class, 'update'])->name('topics.update');
            Route::post('/topics/{topic}/publish', [TopicController::class, 'publish'])->name('topics.publish');
            Route::delete('/topics/{topic}/delete', [TopicController::class, 'destroy'])->name('topics.delete');

            //podcast route
            Route::get('/podcasts/{topic}', [PodcastController::class, 'index'])->name('podcasts.index');
            Route::get('/podcasts/{podcast}', [PodcastController::class, 'show'])->name('podcasts.show');
            Route::get('/podcasts/create', [PodcastController::class, 'create'])->name('podcasts.create');
            Route::post('/podcasts/{topic}', [PodcastController::class, 'store'])->name('podcasts.store');
            Route::get('/podcasts/{podcast}/edit', [PodcastController::class, 'edit'])->name('podcasts.edit');
            Route::patch('/podcasts/{podcast}/update', [PodcastController::class, 'update'])->name('podcasts.update');
            Route::delete('/podcasts/{podcast}/delete', [PodcastController::class, 'destroy'])->name('podcasts.delete');
        });
});
require __DIR__ . '/auth.php';
