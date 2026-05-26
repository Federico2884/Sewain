<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id'      => Rental::factory(),
            'amount'         => 300000,
            'rent_amount'    => 200000,
            'deposit_amount' => 100000,
            'status'         => Payment::STATUS_PENDING,
            'method'         => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status'         => Payment::STATUS_PAID,
            'method'         => Payment::METHOD_GOPAY,
            'transaction_id' => 'TRX-TEST',
            'paid_at'        => now(),
        ]);
    }
}
