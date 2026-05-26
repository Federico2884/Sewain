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
        return [
            'vendor_id'    => Vendor::factory(),
            'name'         => fake()->words(3, true),
            'category'     => fake()->randomElement(['Elektronik', 'Otomotif', 'Olahraga', 'Kamera']),
            'price'        => fake()->numberBetween(50000, 500000),
            'deposit'      => fake()->numberBetween(100000, 1000000),
            'image'        => 'items/placeholder.jpg',
            'availability' => true,
        ];
    }
}
