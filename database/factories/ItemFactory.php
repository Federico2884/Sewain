<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dailyPrice = fake()->numberBetween(50000, 500000);

        return [
            'vendor_id' => Vendor::factory(),
            'name' => fake()->words(3, true),
            'category' => fake()->randomElement(['Elektronik', 'Otomotif', 'Olahraga', 'Kamera']),
            'price' => $dailyPrice,
            'price_jam' => round($dailyPrice / 8, -2),
            'price_bulan' => $dailyPrice * 25,
            'deposit' => fake()->numberBetween(100000, 1000000),
            'image' => 'items/placeholder.jpg',
            'availability' => true,
        ];
    }

    /**
     * Item that is only rentable per day (no hourly/monthly pricing).
     */
    public function dailyOnly(): static
    {
        return $this->state(fn (array $attributes): array => [
            'price_jam' => null,
            'price_bulan' => null,
        ]);
    }
}
