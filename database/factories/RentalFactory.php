<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vendor = Vendor::factory();
        $item   = Item::factory()->state(['vendor_id' => $vendor]);

        return [
            'user_id'   => User::factory(),
            'vendor_id' => $vendor,
            'item_id'   => $item,
            'duration'  => 3,
            'unit'      => 'hari',
            'start'     => now()->toDateString(),
            'method'    => 'pickup',
        ];
    }
}
