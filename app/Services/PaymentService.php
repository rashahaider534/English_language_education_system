<?php

namespace App\Services;

use App\Models\User;
use App\Models\Level;
use App\Models\Payment;
use Stripe\Stripe;
use App\Enums\PaymentStatus;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Validation\ValidationException;

class PaymentService
{

    public function __construct(
        private LevelAccessService $levelAccessService
    ) {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(User $user, Level $level)
    {
        $alreadyPaid = Payment::where('user_id', $user->id)
            ->where('level_id', $level->id)
            ->where('status', PaymentStatus::PAID)
            ->exists();

        if ($alreadyPaid) {
            throw ValidationException::withMessages([
                'level' => __('messages.level_already_purchased'),
            ]);
        }
        $allowedOrder = $this->levelAccessService->getAllowedOrder($user);
        $context = $this->levelAccessService->getUserLevelContext($user);
        $AvailableLevels = $this->levelAccessService->getAvailableLevels(
            $allowedOrder,
            $context['userLevelIds'],
            $context['approvedExceptionLevelIds']
        );

        if (! $AvailableLevels->contains('id', $level->id)) {
            throw ValidationException::withMessages([
                'level' =>__('messages.only_request_available_levels'),
            ]);
        }

        $existingPending = Payment::where('user_id', $user->id)
            ->where('level_id', $level->id)
            ->where('status', PaymentStatus::PENDING)
            ->latest()
            ->first();

        if ($existingPending) {
            try {
                $intent = PaymentIntent::retrieve($existingPending->stripe_payment_intent_id);

                if (in_array($intent->status, ['requires_payment_method', 'requires_confirmation'])) {
                    return [
                        'clientSecret' => $intent->client_secret,
                        'paymentIntentId' => $intent->id,
                    ];
                }
            } catch (ApiErrorException $e) {
                // تجاهل وكمل لإنشاء واحد جديد
            }
        }
        try {
            $intent = PaymentIntent::create([
                'amount' => (int) round($level->price * 100),
                'currency' => 'usd',
                'metadata' => [
                    'user_id' => $user->id,
                    'level_id' => $level->id,
                ],
            ]);
        } catch (ApiErrorException $e) {
            throw new \Exception(__('messages.failed_to_create_payment', ['error' => $e->getMessage()]));
        }

        Payment::create([
            'user_id' => $user->id,
            'level_id' => $level->id,
            'stripe_payment_intent_id' => $intent->id,
            'amount' => $level->price,
            'currency' => 'usd',
            'status' => PaymentStatus::PENDING,
        ]);

        return [
            'clientSecret' => $intent->client_secret,
            'paymentIntentId' => $intent->id,
        ];
    }

    public function getStatus(User $user, string $paymentIntentId): string
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return $payment->status->value;
    }
}
