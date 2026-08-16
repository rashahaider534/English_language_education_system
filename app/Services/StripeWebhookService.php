<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Level;
use App\Models\User;
use App\Enums\PaymentStatus;
use App\Jobs\SendNotificationJob;
use App\Models\UserLevel;

class StripeWebhookService
{
    public function handle($event): void
    {
        $intent = $event->data->object;

        match ($event->type) {
            'payment_intent.succeeded' => $this->markAsPaid($intent),
            'payment_intent.payment_failed' => $this->markAsFailed($intent),
            default => null,
        };
    }

    private function markAsPaid($intent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $intent->id)->first();

        if (! $payment) {
            return;
        }

        $payment->update([
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        UserLevel::create([
            'user_id' => $payment->user_id,
            'level_id' => $payment->level_id,
            'status' => 'in_progress',
            'enrolled_at' => now(),
        ]);

        SendNotificationJob::dispatch(
            [$payment->user_id],
            'Level Unlocked',
            "The level {$payment->level->name_en} has been successfully unlocked. You can now start learning.",
            [
                'level_id' => $payment->level_id,
            ],
            'level-opened'
        );
    }

    private function markAsFailed($intent): void
    {
        Payment::where('stripe_payment_intent_id', $intent->id)->update([
            'status' => PaymentStatus::FAILED,
            'failure_reason' => $intent->last_payment_error->message ?? 'Unknown error',
        ]);
    }
}
