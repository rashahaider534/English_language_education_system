<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\web\ContentReview\RequestChangesRequest;
use App\Models\ContentReview;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Test;
use App\Services\ContentReview\AdminContentReviewService;
use App\Services\ContentReview\ContentReviewService;
use App\Services\Test\AdminTestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminContentReviewController extends Controller
{
    public function __construct(
        private AdminContentReviewService $adminContentReviewService,
        private ContentReviewService $contentReviewService,
        private AdminTestService $adminTestService,
    ) {}

    /**
     * طابور المحتوى الذي ينتظر قبول مهمة (claim) من الأدمن.
     */
    public function pendingQueue(): View
    {
        $queue = $this->adminContentReviewService->pendingContentQueue();

        return view('admin.content-review.pending-queue', [
            'queue' => $queue,
        ]);

    }

    /**
     * جلسات التدقيق الحالية الخاصة بالأدمن (المسجل دخوله).
     */
    public function mySessions(): View
    {
        $status = request('status');

        $sessions = $this->adminContentReviewService->myReviewSessions($status);

        return view('admin.content-review.my-sessions', [
            'sessions' => $sessions,
            'currentStatus' => $status,
        ]);

    }

    /**
     * قبول مهمة تدقيق درس (مع اختباره).
     */
    public function claimLesson(Lesson $lesson): RedirectResponse
    {
        $this->adminContentReviewService->claimLesson($lesson);

        return redirect()
            ->route('admin.content-review.my-sessions')
            ->with('success', 'تم استلام الدرس واختباره للتدقيق بنجاح.');
    }

    /**
     * قبول مهمة تدقيق اختبار مستقل (كورس أو نسخة اختبار درس).
     */
    public function claimTest(Test $test): RedirectResponse
    {
        $this->adminContentReviewService->claimTest($test);

        return redirect()
            ->route('admin.content-review.my-sessions')
            ->with('success', 'تم استلام الاختبار للتدقيق بنجاح.');
    }

    /**
     * الموافقة على جلسة تدقيق.
     */
    public function approve(ContentReview $review): RedirectResponse
    {
        $this->adminContentReviewService->approve($review);

        return redirect()
            ->route('admin.content-review.my-sessions')
            ->with('success', 'تمت الموافقة بنجاح.');
    }

    /**
     * طلب تعديلات على جلسة تدقيق.
     */
    public function requestChanges(RequestChangesRequest $request, ContentReview $review): RedirectResponse
    {
        $this->adminContentReviewService->requestChanges($review, $request->validated('message'));

        return redirect()
            ->route('admin.content-review.my-sessions')
            ->with('success', 'تم إرسال طلب التعديلات بنجاح.');
    }

    /**
     * التخلي عن جلسة تدقيق دون اتخاذ قرار.
     */
    public function release(ContentReview $review): RedirectResponse
    {
        $this->adminContentReviewService->release($review);

        return redirect()
            ->route('admin.content-review.my-sessions')
            ->with('success', 'تم التخلي عن الجلسة وإعادتها للطابور.');
    }

    /**
     * الموافقة المباشرة (اختبار مستوى / placement) بدون جلسة تدقيق.
     */
    public function approveDirectly(Test $test): RedirectResponse
    {
        $this->adminContentReviewService->approveDirectly($test);

        return redirect()
            ->back()
            ->with('success', 'تمت الموافقة على الاختبار مباشرة.');
    }

    /**
     * إرجاع درس معتمَد سابقاً لطلب تعديلات عليه.
     */
    public function revertApprovedLesson(RequestChangesRequest $request, Lesson $lesson): RedirectResponse
    {
        $this->adminContentReviewService->revertApprovedLesson($lesson, $request->validated('message'));

        return redirect()
            ->back()
            ->with('success', 'تم إرجاع الدرس لطلب التعديلات.');
    }

    /**
     * نشر اختبار بعد الموافقة عليه.
     */
    public function publishTest(Test $test): RedirectResponse
    {
        $this->adminContentReviewService->publishTest($test);

        return redirect()
            ->back()
            ->with('success', 'تم نشر الاختبار بنجاح.');
    }

    /**
     * عرض تفاصيل درس مع كل نسخ اختباراته وحالاتها.
     */
    public function showLessonTestVersions(Lesson $lesson): View
    {
        $testVersions = $this->adminTestService->LessontestVersions($lesson);

        return view('admin.lesson-show', [
            'testVersions' => $testVersions,
        ]);
    }
    public function showCourseTestVersions(Course $course)
    //: View
    {
        $testVersions = $this->adminTestService->CoursetestVersions($course);

//        return view('admin.course-show', [
//            'testVersions' => $testVersions,
//        ]);
        return response()->json($testVersions);
    }

    /**
     * تاريخ جلسات التدقيق الكامل لدرس.
     */
    public function lessonHistory(Lesson $lesson): View
    {
        $history = $this->contentReviewService->reviewHistory($lesson);

        return view('admin.content-review.history', [
            'reviewable' => $lesson,
            'history' => $history,
            'title' => 'سجل تدقيق الدرس: ' . $lesson->title_ar,
        ]);

    }

    /**
     * تاريخ جلسات التدقيق الكامل لاختبار.
     */
    public function testHistory(Test $test)
    //: View
    {
        $history = $this->contentReviewService->reviewHistory($test);

        return view('admin.content-review.history', [
            'reviewable' => $test,
            'history' => $history,
            'title' => 'سجل تدقيق الاختبار: ' . $test->title_ar,
        ]);

    }

    /**
     * نشر مستوى كامل بعد اعتماد كل دروسه وكورساته واختباراته.
     */
    public function publishLevel(Level $level): RedirectResponse
    {
        $this->adminContentReviewService->publishLevel($level);

        return redirect()
            ->back()
            ->with('success', 'تم نشر المستوى بنجاح.');
    }
}
