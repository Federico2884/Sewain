<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_with_category_shortcuts(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);

        // Every curated category is shown, even ones without any items yet.
        foreach (array_keys(Item::CATEGORIES) as $category) {
            $response->assertSee($category);
        }
    }

    public function test_home_page_lists_available_items(): void
    {
        $vendor = Vendor::factory()->create();
        $item = Item::factory()->create([
            'vendor_id' => $vendor->id,
            'availability' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_catalog_shows_canonical_categories_even_without_items(): void
    {
        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('Mobil');
        $response->assertSee('Kamera');
    }

    public function test_catalog_filters_by_category(): void
    {
        $vendor = Vendor::factory()->create();
        $motor = Item::factory()->create([
            'vendor_id' => $vendor->id,
            'name' => 'Honda Beat Sporty',
            'category' => 'Sepeda Motor',
            'availability' => true,
        ]);
        $camera = Item::factory()->create([
            'vendor_id' => $vendor->id,
            'name' => 'Sony Alpha A7',
            'category' => 'Kamera',
            'availability' => true,
        ]);

        $response = $this->get(route('items.index', ['category' => 'Sepeda Motor']));

        $response->assertStatus(200);
        $response->assertSee($motor->name);
        $response->assertDontSee($camera->name);
    }
}
