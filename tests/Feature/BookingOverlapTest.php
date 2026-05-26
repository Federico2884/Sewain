<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingOverlapTest extends TestCase
{
    use RefreshDatabase;

    private function makeActiveRental(Item $item, string $start, int $duration = 3, string $unit = 'hari'): Rental
    {
        $rental = Rental::factory()->create([
            'user_id'   => User::factory(),
            'vendor_id' => $item->vendor_id,
            'item_id'   => $item->id,
            'duration'  => $duration,
            'unit'      => $unit,
            'start'     => $start,
        ]);

        Payment::factory()->paid()->create(['rental_id' => $rental->id]);

        return $rental;
    }

    public function test_catalog_still_shows_item_that_is_currently_being_rented(): void
    {
        $vendor = Vendor::factory()->create();
        $item   = Item::factory()->create(['vendor_id' => $vendor->id, 'availability' => true]);

        $this->makeActiveRental($item, now()->toDateString(), 5);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_item_show_page_marks_status_as_currently_rented_but_still_lets_user_browse(): void
    {
        $user   = User::factory()->create();
        $vendor = Vendor::factory()->create();
        $item   = Item::factory()->create(['vendor_id' => $vendor->id, 'availability' => true]);

        $this->makeActiveRental($item, now()->toDateString(), 2);

        $response = $this->actingAs($user)->get(route('items.show', $item));

        $response->assertStatus(200);
        $response->assertSee('Sedang disewa', false);
    }

    public function test_booking_overlapping_with_existing_rental_is_rejected(): void
    {
        $user   = User::factory()->create();
        $vendor = Vendor::factory()->create();
        $item   = Item::factory()->create(['vendor_id' => $vendor->id, 'availability' => true]);

        $existingStart = now()->addDays(2)->toDateString();
        $this->makeActiveRental($item, $existingStart, 5);

        $response = $this->actingAs($user)->post(route('user.rentals.store', $item), [
            'duration' => 2,
            'unit'     => 'hari',
            'start'    => now()->addDays(3)->toDateString(),
            'method'   => 'pickup',
        ]);

        $response->assertSessionHasErrors('start');
        $this->assertNull(session('booking'));
    }

    public function test_booking_outside_rented_window_is_accepted(): void
    {
        $user   = User::factory()->create();
        $vendor = Vendor::factory()->create();
        $item   = Item::factory()->create(['vendor_id' => $vendor->id, 'availability' => true]);

        $this->makeActiveRental($item, now()->toDateString(), 3, 'hari');

        $futureStart = now()->addDays(10)->toDateString();

        $response = $this->actingAs($user)->post(route('user.rentals.store', $item), [
            'duration' => 2,
            'unit'     => 'hari',
            'start'    => $futureStart,
            'method'   => 'pickup',
        ]);

        $response->assertRedirect(route('user.rentals.checkout', $item));
        $this->assertSame($item->id, session('booking.item_id'));
        $this->assertSame($futureStart, session('booking.start'));
    }

    public function test_paying_for_rental_does_not_flip_item_availability_off(): void
    {
        $user   = User::factory()->create();
        $vendor = Vendor::factory()->create();
        $item   = Item::factory()->create(['vendor_id' => $vendor->id, 'availability' => true]);

        $this->makeActiveRental($item, now()->toDateString(), 4);

        $this->assertTrue($item->fresh()->availability);
    }

    public function test_item_overlaps_booked_dates_matches_active_rental_window(): void
    {
        $vendor = Vendor::factory()->create();
        $item   = Item::factory()->create(['vendor_id' => $vendor->id, 'availability' => true]);

        $start = now()->addDay()->startOfDay();
        $this->makeActiveRental($item, $start->toDateString(), 3, 'hari');

        $item->refresh();

        $this->assertTrue($item->overlapsBookedDates($start->copy()->addDay(), 1, 'hari'));
        $this->assertFalse($item->overlapsBookedDates($start->copy()->addDays(10), 1, 'hari'));
    }
}
