<?php

use App\Http\Controllers\Admin\AdminContentReviewController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\DashboardTemplateController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\LevelExceptionController;
use App\Http\Controllers\Admin\TeacherManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\PermissionManagementController;
use App\Http\Controllers\Admin\PaymentManagementController;
use App\Http\Controllers\Admin\AuditController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardTemplateController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/profile', [DashboardTemplateController::class, 'profile'])->name('dashboard.profile');
    Route::get('/dashboard/settings', [DashboardTemplateController::class, 'settings'])->name('dashboard.settings');
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
    //permissions route
    Route::get('/permissions/index', [PermissionController::class, 'index'])->name('admin.permission.index');
    Route::get('/permission/{user}/showadmin', [PermissionController::class, 'getAdmin'])->name('admin.permission.show');
    Route::get('/permission/admins/create', [PermissionController::class, 'createadmin'])->name('admins.permission.create');
    Route::post('/permission/admin', [PermissionController::class, 'storeAdmin'])->name('admins.permission.store');
    Route::delete('/permission/{user}/delete', [PermissionController::class, 'destroy'])->name('admins.permission.destroy');
    Route::get('/permission/{user}/admin', [PermissionController::class, 'choosePermissions'])->name('admin.permissions');
    Route::post('/permission/{user}/assign', [PermissionController::class, 'assignPermissions'])->name('admin.permission.assignPermissions');
    Route::post('/permission/{user}/revoke', [PermissionController::class, 'revokePermissions'])->name('admin.permission.revokePermissions');
    Route::get('/permission/teacher/create', [PermissionController::class, 'createteacher'])->name('admin.permission.teacher.create');
    Route::post('/permission/teacher', [PermissionController::class, 'storeTeacher'])->name('admin.permission.teacher.store');

    // Admin management (super-admin only)
    Route::prefix('management/admins')->name('admin.admins.')->group(function () {
        Route::get('/', [AdminManagementController::class, 'index'])->name('index');
        Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
        Route::post('/', [AdminManagementController::class, 'store'])->name('store');
        Route::patch('/{admin}/toggle-active', [AdminManagementController::class, 'toggleActive'])->name('toggle-active');
    });

    // Discounts & offers
    Route::prefix('offers')->name('admin.offers.')->group(function () {
        Route::get('/', [OfferController::class, 'index'])->name('index');
        Route::get('/create', [OfferController::class, 'create'])->name('create');
        Route::post('/', [OfferController::class, 'store'])->name('store');
    });

    // Permissions
    Route::prefix('permissions')->name('admin.permissions.')->group(function () {
        Route::get('/', [PermissionManagementController::class, 'index'])->name('index');
        Route::post('/', [PermissionManagementController::class, 'update'])->name('update');
    });

    // Payments
    Route::get('/payments', [PaymentManagementController::class, 'index'])->name('admin.payments.index');

    // Audit & business management
    Route::prefix('audit')->name('admin.audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/levels/{level}', [AuditController::class, 'level'])->name('level');
    });
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
    Route::get('lessons/{lesson}/tests', [AdminContentReviewController::class, 'showLessonTestVersions'])->name('lessons.tests.show');
    //comment route
    Route::delete('/comments/{comment}/destroy', [CommentController::class, 'admindelete']);
    Route::patch('/comments/{comment}/block', [CommentController::class, 'block']);

    Route::patch('/comments/{comment}/block',[CommentController::class,'block']);

    // Teachers management & monitoring — index/lessons query real data;
    // create/store/toggle-active are UI-only placeholders pending backend work.
    Route::prefix('management/teachers')->name('admin.teachers.')->group(function () {
        Route::get('/', [TeacherManagementController::class, 'index'])->name('index');
        Route::get('/create', [TeacherManagementController::class, 'create'])->name('create');
        Route::post('/', [TeacherManagementController::class, 'store'])->name('store');
        Route::get('/{teacher}/lessons', [TeacherManagementController::class, 'lessons'])->name('lessons');
        Route::patch('/{teacher}/toggle-active', [TeacherManagementController::class, 'toggleActive'])->name('toggle-active');
    });

    // Students management & monitoring — index queries real data;
    // ban is a UI-only placeholder, no ban system exists yet.
    Route::prefix('management/students')->name('admin.students.')->group(function () {
        Route::get('/', [StudentManagementController::class, 'index'])->name('index');
        Route::patch('/{student}/ban', [StudentManagementController::class, 'ban'])->name('ban');
    });

    // Complaints inbox (contact_us table) — read-only listing, no actions yet.
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('admin.complaints.index');
});

Route::middleware(['auth:web'])->group(function () {
    
    Route::middleware(['role:admin|super-admin', 'permission:manage_comment'])
        ->group(function () {
            //comment route
            Route::delete('/comments/{comment}/destroy', [CommentController::class, 'admindelete']);
            Route::patch('/comments/{comment}/block', [CommentController::class, 'block']);
        });

    Route::middleware(['role:admin|super-admin', 'permission:manage_levelexception'])
        ->group(function () {
            //Level Exception route
            Route::get('/levelexceptions', [LevelExceptionController::class, 'index'])->name('levelException.index');
            Route::get('/levelexceptions/{levelException}/details', [LevelExceptionController::class, 'show'])->name('levelException.show');
            Route::patch('/levelexceptions/{levelException}/start', [LevelExceptionController::class, 'startReview'])->name('levelException.review');
            Route::patch('/levelexceptions/{levelException}/approve', [LevelExceptionController::class, 'approve'])->name('levelException.approve');
            Route::patch('/levelexceptions/{levelException}/reject', [LevelExceptionController::class, 'reject'])->name('levelException.reject');
        });

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

    Route::middleware(['role:admin|super-admin', 'permission:manage_podcasts'])
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
            Route::get('/podcasts/create', [PodcastController::class, 'create'])->name('podcasts.create');
            Route::get('/topics/{topic}/podcasts', [PodcastController::class, 'index'])->name('podcasts.index');
            Route::post('/topics/{topic}/podcasts', [PodcastController::class, 'store'])->name('podcasts.store');
            Route::get('/podcasts/{podcast}', [PodcastController::class, 'show'])->name('podcasts.show');
            Route::get('/podcasts/{podcast}/edit', [PodcastController::class, 'edit'])->name('podcasts.edit');
            Route::patch('/podcasts/{podcast}/update', [PodcastController::class, 'update'])->name('podcasts.update');
            Route::delete('/podcasts/{podcast}/delete', [PodcastController::class, 'destroy'])->name('podcasts.delete');
        });

    Route::middleware(['role:admin'])
        ->prefix('admin/content-review')
        ->name('admin.content-review.')
        ->group(function () {
            Route::get('pending-queue', [AdminContentReviewController::class, 'pendingQueue'])->name('pending-queue');
            Route::get('my-sessions', [AdminContentReviewController::class, 'mySessions'])->name('my-sessions');

            Route::post('lessons/{lesson}/claim', [AdminContentReviewController::class, 'claimLesson'])->name('lessons.claim');
            Route::post('tests/{test}/claim', [AdminContentReviewController::class, 'claimTest'])->name('tests.claim');

            Route::post('reviews/{review}/approve', [AdminContentReviewController::class, 'approve'])->name('reviews.approve');
            Route::post('reviews/{review}/request-changes', [AdminContentReviewController::class, 'requestChanges'])->name('reviews.request-changes');
            Route::post('reviews/{review}/release', [AdminContentReviewController::class, 'release'])->name('reviews.release');

            Route::post('tests/{test}/approve-directly', [AdminContentReviewController::class, 'approveDirectly'])->name('tests.approve-directly');
            Route::post('tests/{test}/publish', [AdminContentReviewController::class, 'publishTest'])->name('tests.publish');

            Route::post('lessons/{lesson}/revert', [AdminContentReviewController::class, 'revertApprovedLesson'])->name('lessons.revert');

            Route::get('lessons/{lesson}/history', [AdminContentReviewController::class, 'lessonHistory'])->name('lessons.history');
            Route::get('tests/{test}/history', [AdminContentReviewController::class, 'testHistory'])->name('tests.history');

            Route::post('levels/{level}/publish', [AdminContentReviewController::class, 'publishLevel'])->name('levels.publish');
        });
});
require __DIR__ . '/auth.php';
