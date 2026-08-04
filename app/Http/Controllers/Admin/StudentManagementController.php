<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $levelId = $request->query('level');

        $students = User::role('student')
            ->with(['studentProfile', 'userLevels' => function ($query) {
                $query->latest('enrolled_at')->with('level');
            }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($levelId, function ($query) use ($levelId) {
                $query->whereHas('userLevels', function ($q) use ($levelId) {
                    $q->where('level_id', $levelId);
                });
            })
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        // "المستوى الحالي" مُشتق من آخر تسجيل بجدول user_levels (أحدث enrolled_at) —
        // هذا قرار مبدئي لحين تحديد المنطق النهائي وقت بناء الباك-إند.
        foreach ($students as $student) {
            $student->current_level = optional($student->userLevels->first())->level;
        }

        $levels = Level::orderBy('order')->get();

        $totalCount = User::role('student')->count();

        // بيانات placeholder لإحصائية توزع المستويات (الاستعلام الحقيقي بسيط وجاهز لاحقًا:
        // Level::withCount('users')->get()) — بس هلق بيانات تصميم فقط، متل باقي الصفحة.
        $levelDistribution = $levels->map(function ($level, $index) {
            $sample = [14, 9, 5, 2];
            return [
                'label' => $level->name_ar ?? $level->name_en,
                'count' => $sample[$index] ?? random_int(1, 10),
            ];
        });
        $levelDistributionMax = max(1, $levelDistribution->max('count') ?? 1);

        return view('admin.students.index', compact(
            'students',
            'search',
            'levelId',
            'levels',
            'totalCount',
            'levelDistribution',
            'levelDistributionMax'
        ));
    }

    public function ban(User $student): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: ما في نظام حظر حسابات كامل بالتطبيق حاليًا إطلاقًا.
        return redirect()->route('admin.students.index')
            ->with('info', 'ميزة حظر الطلاب قيد التطوير، وسيتم بناؤها وربطها بالباك-إند لاحقًا.');
    }
}
