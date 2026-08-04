<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Models\Level;

class PaymentController extends Controller
{
     public function __construct(private PaymentService $paymentService) {}

    public function createIntent(Request $request, Level $level)
    {
        $result = $this->paymentService->createPaymentIntent(
            $request->user(),
            $level
        );

        return response()->json($result);
    }

    public function status(Request $request,string $paymentIntentId)
    {
        $status = $this->paymentService->getStatus($request->user(), $paymentIntentId);

        return response()->json(['status' => $status]);
    }
}
