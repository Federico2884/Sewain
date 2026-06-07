<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

/**
 * Deterministic data for the Selenium (Python) browser test suite.
 *
 * Idempotent — safe to run repeatedly. All accounts use the password
 * "password". Run with: php artisan db:seed --class=SeleniumSeeder
 */
class SeleniumSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstWhere('email', 'user@selenium.test')
            ?? User::factory()->create([
                'name' => 'Selenium User',
                'email' => 'user@selenium.test',
            ]);

        $vendor = Vendor::firstWhere('email', 'vendor@selenium.test')
            ?? Vendor::factory()->create([
                'name' => 'Selenium Vendor',
                'email' => 'vendor@selenium.test',
                'rating' => 4.5,
            ]);

        $items = [
            ['name' => 'Selenium Kamera Sony A7', 'category' => 'Kamera'],
            ['name' => 'Selenium Drone DJI Mini', 'category' => 'Elektronik'],
            ['name' => 'Selenium Tenda Gunung 4 Orang', 'category' => 'Alat Kemah'],
        ];

        foreach ($items as $data) {
            if (! Item::where('name', $data['name'])->exists()) {
                Item::factory()->create([
                    'vendor_id' => $vendor->id,
                    'name' => $data['name'],
                    'category' => $data['category'],
                    'availability' => true,
                ]);
            }
        }

        $this->command?->info('Selenium test data ready:');
        $this->command?->line("  User   : {$user->email} / password");
        $this->command?->line("  Vendor : {$vendor->email} / password");
        $this->command?->line('  Items  : '.count($items).' available items');
    }
}
