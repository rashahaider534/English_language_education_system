<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Management\PaymentManagementService;
class PaymentManagementController extends Controller
{
    public function __construct(
        protected PaymentManagementService $paymentService
    ) {}
    public function index(Request $request): View
    {
          $status = $request->query('status');

        $payments = $this->paymentService->getPayments($status);

        $statistics = $this->paymentService->getStatistics();


        return view('admin.payments.index',
        ['payments'=>$payments, 'status'=>$status,
         'totalRevenue'=>$statistics['totalRevenue']
         , 'paidCount'=>$statistics['paidCount'],
          'pendingCount'=>$statistics['pendingCount'],
           'failedCount'=>$statistics['failedCount']]);
    }
}
