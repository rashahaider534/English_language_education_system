<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Level;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::role('student')->get();

        $levels = Level::orderBy('id')->take(3)->get();

        if ($students->count() < 10) {
            return;
        }

        if ($levels->count() < 3) {
            return;
        }

        foreach ($students->take(10) as $index => $student) {

            $level = $levels[$index % 3];

            $status = match ($index) {
                0, 1, 2, 3, 4, 5 => PaymentStatus::PAID,
                6, 7 => PaymentStatus::PENDING,
                default => PaymentStatus::FAILED,
            };

            $payment = [
                'user_id' => $student->id,
                'level_id' => $level->id,

                'stripe_payment_intent_id' =>
                'pi_test_' . uniqid(),

                'amount' => match ($level->id) {
                    $levels[0]->id => 49.99,
                    $levels[1]->id => 59.99,
                    default => 69.99,
                },

                'currency' => 'usd',

                'status' => $status->value,

                'paid_at' => null,

                'failure_reason' => null,
            ];

            if ($status === PaymentStatus::PAID) {
                $payment['paid_at'] = now()->subDays(rand(0, 10));
            }

            if ($status === PaymentStatus::FAILED) {
                $payment['failure_reason'] =
                    'The payment was declined by the payment provider.';
            }

            Payment::create($payment);
        }
    }
}
