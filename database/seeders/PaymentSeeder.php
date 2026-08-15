<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payments = [
            // User 2
            [
                'user_id' => 2,
                'level_id' => 1,
                'stripe_payment_intent_id' => 'pi_test_user2_level1',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(60),
            ],
            [
                'user_id' => 2,
                'level_id' => 2,
                'stripe_payment_intent_id' => 'pi_test_user2_level2',
                'amount' => 70.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(30),
            ],

            // User 9
            [
                'user_id' => 9,
                'level_id' => 1,
                'stripe_payment_intent_id' => 'pi_test_user9_level1',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(45),
            ],
            [
                'user_id' => 9,
                'level_id' => 2,
                'stripe_payment_intent_id' => 'pi_test_user9_level2',
                'amount' => 70.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(20),
            ],

            // User 10
            [
                'user_id' => 10,
                'level_id' => 1,
                'stripe_payment_intent_id' => 'pi_test_user10_level1',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(90),
            ],
            [
                'user_id' => 10,
                'level_id' => 2,
                'stripe_payment_intent_id' => 'pi_test_user10_level2',
                'amount' => 70.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(60),
            ],
            [
                'user_id' => 10,
                'level_id' => 3,
                'stripe_payment_intent_id' => 'pi_test_user10_level3',
                'amount' => 90.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(30),
            ],

            // User 11
            [
                'user_id' => 11,
                'level_id' => 1,
                'stripe_payment_intent_id' => 'pi_test_user11_level1',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(25),
            ],

            // User 12
            [
                'user_id' => 12,
                'level_id' => 1,
                'stripe_payment_intent_id' => 'pi_test_user12_level1',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(50),
            ],
            [
                'user_id' => 12,
                'level_id' => 2,
                'stripe_payment_intent_id' => 'pi_test_user12_level2',
                'amount' => 70.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(20),
            ],

            // User 13
            [
                'user_id' => 13,
                'level_id' => 1,
                'stripe_payment_intent_id' => 'pi_test_user13_level1',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(15),
            ],

            // User 14
            [
                'user_id' => 14,
                'level_id' => 1,
                'stripe_payment_intent_id' => 'pi_test_user14_level1',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(40),
            ],
            [
                'user_id' => 14,
                'level_id' => 2,
                'stripe_payment_intent_id' => 'pi_test_user14_level2',
                'amount' => 70.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(10),
            ],

            // User 15
            [
                'user_id' => 15,
                'level_id' => 1,
                'stripe_payment_intent_id' => 'pi_test_user15_level1',
                'amount' => 50.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(120),
            ],
            [
                'user_id' => 15,
                'level_id' => 2,
                'stripe_payment_intent_id' => 'pi_test_user15_level2',
                'amount' => 70.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(90),
            ],
            [
                'user_id' => 15,
                'level_id' => 3,
                'stripe_payment_intent_id' => 'pi_test_user15_level3',
                'amount' => 90.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(60),
            ],
            [
                'user_id' => 15,
                'level_id' => 4,
                'stripe_payment_intent_id' => 'pi_test_user15_level4',
                'amount' => 110.00,
                'currency' => 'usd',
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now()->subDays(30),
            ],
        ];

        foreach ($payments as $payment) {
            Payment::create($payment);
        }
    }
}
