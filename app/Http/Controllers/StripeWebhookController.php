<?php

namespace App\Http\Controllers;
use App\Services\StripeWebhookService;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __construct(private StripeWebhookService $webhookService) {}
    
     public function handle(Request $request)
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret')
            );
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid webhook signature'], 400);
        }

        $this->webhookService->handle($event);

        return response()->json(['status' => 'success']);
    }
}
