<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'           => fake()->company(),
            'email'          => fake()->unique()->safeEmail(),
            'password'       => Hash::make('password'),
            'ktp'            => fake()->numerify('################'),
            'address'        => fake()->address(),
            'total_star'     => 0,
            'reviewer_count' => 0,
            'rating'         => 0,
        ];
    }
}
