<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemDescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_can_create_item_with_description(): void
    {
        Storage::fake('public');
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($vendor, 'vendor')->post(route('vendor.items.store'), [
            'name' => 'Kamera Mirrorless',
            'description' => 'Kondisi mulus, lengkap dengan lensa kit dan tas.',
            'category' => 'Kamera',
            'price' => 100000,
            'deposit' => 50000,
            'availability' => 1,
            'image' => UploadedFile::fake()->image('kamera.jpg'),
        ]);

        $response->assertRedirect(route('vendor.items.index'));
        $this->assertDatabaseHas('items', [
            'name' => 'Kamera Mirrorless',
            'description' => 'Kondisi mulus, lengkap dengan lensa kit dan tas.',
            'vendor_id' => $vendor->id,
        ]);
    }

    public function test_item_can_be_created_without_description(): void
    {
        Storage::fake('public');
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($vendor, 'vendor')->post(route('vendor.items.store'), [
            'name' => 'Tenda Gunung',
            'category' => 'Alat Kemah',
            'price' => 50000,
            'deposit' => 25000,
            'availability' => 1,
            'image' => UploadedFile::fake()->image('tenda.jpg'),
        ]);

        $response->assertRedirect(route('vendor.items.index'));
        $this->assertDatabaseHas('items', [
            'name' => 'Tenda Gunung',
            'description' => null,
        ]);
    }

    public function test_vendor_can_update_item_description(): void
    {
        Storage::fake('public');
        $vendor = Vendor::factory()->create();
        $item = Item::factory()->for($vendor)->create([
            'description' => 'Deskripsi lama.',
        ]);

        $response = $this->actingAs($vendor, 'vendor')->put(route('vendor.items.update', $item), [
            'name' => $item->name,
            'description' => 'Deskripsi baru yang diperbarui.',
            'category' => $item->category,
            'price' => $item->price,
            'deposit' => $item->deposit,
            'availability' => 1,
        ]);

        $response->assertRedirect(route('vendor.items.index'));
        $this->assertSame('Deskripsi baru yang diperbarui.', $item->fresh()->description);
    }

    public function test_description_is_shown_on_item_detail_page(): void
    {
        $item = Item::factory()->create([
            'description' => 'Deskripsi unik untuk pengujian detail barang.',
            'availability' => true,
        ]);

        $response = $this->get(route('items.show', $item));

        $response->assertStatus(200);
        $response->assertSee('Deskripsi unik untuk pengujian detail barang.');
    }

    public function test_description_over_max_length_is_rejected(): void
    {
        Storage::fake('public');
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($vendor, 'vendor')->post(route('vendor.items.store'), [
            'name' => 'Barang Deskripsi Panjang',
            'description' => str_repeat('a', 2001),
            'category' => 'Kamera',
            'price' => 100000,
            'deposit' => 50000,
            'availability' => 1,
            'image' => UploadedFile::fake()->image('x.jpg'),
        ]);

        $response->assertSessionHasErrors('description');
    }
}
