<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitPricingTest extends TestCase
{
    use RefreshDatabase;

    private function pricedItem(): Item
    {
        return Item::factory()->create([
            'vendor_id' => Vendor::factory(),
            'price' => 100000,
            'price_jam' => 20000,
            'price_bulan' => 2500000,
            'deposit' => 50000,
            'availability' => true,
        ]);
    }

    public function test_total_cost_uses_the_price_for_the_selected_unit(): void
    {
        $item = $this->pricedItem();

        $perJam = Rental::factory()->create(['item_id' => $item->id, 'vendor_id' => $item->vendor_id, 'unit' => 'jam', 'duration' => 3]);
        $perHari = Rental::factory()->create(['item_id' => $item->id, 'vendor_id' => $item->vendor_id, 'unit' => 'hari', 'duration' => 3]);
        $perBulan = Rental::factory()->create(['item_id' => $item->id, 'vendor_id' => $item->vendor_id, 'unit' => 'bulan', 'duration' => 2]);

        $this->assertSame(60000.0, $perJam->totalCost());     // 20.000 × 3
        $this->assertSame(300000.0, $perHari->totalCost());   // 100.000 × 3
        $this->assertSame(5000000.0, $perBulan->totalCost()); // 2.500.000 × 2
    }

    public function test_price_for_falls_back_to_daily_price_when_unit_is_not_offered(): void
    {
        $item = Item::factory()->dailyOnly()->create(['price' => 80000]);

        $this->assertSame(80000.0, $item->priceFor('jam'));
        $this->assertSame(80000.0, $item->priceFor('bulan'));
        $this->assertSame(80000.0, $item->priceFor('hari'));
    }

    public function test_offered_unit_prices_only_includes_units_the_vendor_priced(): void
    {
        $full = $this->pricedItem();
        $daily = Item::factory()->dailyOnly()->create();

        $this->assertSame(['jam', 'hari', 'bulan'], array_keys($full->offeredUnitPrices()));
        $this->assertSame(['hari'], array_keys($daily->offeredUnitPrices()));
    }

    public function test_checkout_summary_reflects_the_selected_unit_price(): void
    {
        $user = User::factory()->create();
        $item = $this->pricedItem();

        $this->actingAs($user)->post(route('user.rentals.store', $item), [
            'duration' => 3,
            'unit' => 'jam',
            'start' => now()->toDateString(),
            'method' => 'pickup',
        ]);

        $response = $this->actingAs($user)->get(route('user.rentals.checkout', $item));

        $response->assertStatus(200);
        $response->assertSee('Rp 60.000');      // 20.000 × 3 jam
        $response->assertDontSee('Rp 300.000'); // not the daily rate
    }

    public function test_booking_with_a_unit_the_vendor_does_not_offer_is_rejected(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->dailyOnly()->create(['availability' => true]);

        $response = $this->actingAs($user)->post(route('user.rentals.store', $item), [
            'duration' => 2,
            'unit' => 'jam',
            'start' => now()->toDateString(),
            'method' => 'pickup',
        ]);

        $response->assertSessionHasErrors('unit');
        $this->assertNull(session('booking'));
    }
}
