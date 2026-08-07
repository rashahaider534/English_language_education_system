<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        // لسا ما في جدول عروض/خصومات حقيقي بالباك-إند — هاي بيانات توضيحية فقط لعرض شكل الواجهة.
        $courses = Course::with('level')->get();

        $offers = collect([
            [
                'course' => $courses->firstWhere('id', 9) ?? $courses->first(),
                'discount' => 25,
                'starts_at' => now()->subDays(3),
                'ends_at' => now()->addDays(4),
                'status' => 'active',
            ],
            [
                'course' => $courses->firstWhere('id', 10) ?? $courses->skip(1)->first(),
                'discount' => 15,
                'starts_at' => now()->subDays(20),
                'ends_at' => now()->addDays(10),
                'status' => 'active',
            ],
            [
                'course' => $courses->first(),
                'discount' => 30,
                'starts_at' => now()->subMonths(2),
                'ends_at' => now()->subMonth(),
                'status' => 'expired',
            ],
        ])->filter(fn ($o) => $o['course'] !== null)->values();

        $activeOffers = $offers->where('status', 'active')->values();
        $expiredOffers = $offers->where('status', 'expired')->values();

        return view('admin.offers.index', compact('activeOffers', 'expiredOffers', 'courses'));
    }

    public function create(): View
    {
        $courses = Course::with('level')->get();

        return view('admin.offers.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: ما في جدول عروض/خصومات حقيقي لهلق (نموذج تصميم فقط).
        return redirect()->route('admin.offers.index')
            ->with('info', 'إنشاء العروض والخصومات ميزة قيد التطوير، وسيتم ربطها بالباك-إند لاحقًا.');
    }
}
