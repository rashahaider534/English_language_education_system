<?php

namespace App\Services\Management;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentManagementService{

 public function getPayments(?string $status = null): LengthAwarePaginator
    {
        return Payment::with(['user', 'level'])
            ->when(
                $status,
                fn ($query) => $query->where('status', $status)
            )
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();
    }

    public function getStatistics(): array
    {
        return [
            'totalRevenue' => Payment::where('status', PaymentStatus::PAID)
                ->sum('amount'),

            'paidCount' => Payment::where('status', PaymentStatus::PAID)
                ->count(),

            'pendingCount' => Payment::where('status', PaymentStatus::PENDING)
                ->count(),

            'failedCount' => Payment::where('status', PaymentStatus::FAILED)
                ->count(),
        ];
    }
}
