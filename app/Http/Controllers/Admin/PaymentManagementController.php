<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentManagementController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $payments = Payment::with(['user', 'level'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        $totalRevenue = Payment::where('status', PaymentStatus::PAID)->sum('amount');
        $paidCount = Payment::where('status', PaymentStatus::PAID)->count();
        $pendingCount = Payment::where('status', PaymentStatus::PENDING)->count();
        $failedCount = Payment::where('status', PaymentStatus::FAILED)->count();

        return view('admin.payments.index', compact('payments', 'status', 'totalRevenue', 'paidCount', 'pendingCount', 'failedCount'));
    }
}
