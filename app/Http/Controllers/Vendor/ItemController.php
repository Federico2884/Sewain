<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    private function vendor()
    {
        return Auth::guard('vendor')->user();
    }

    public function index()
    {
        $items = $this->vendor()
            ->items()
            ->withCount('rentals')
            ->latest()
            ->paginate(10);

        return view('vendor.items.index', compact('items'));
    }

    public function create()
    {
        return view('vendor.items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_jam' => ['nullable', 'numeric', 'min:0'],
            'price_bulan' => ['nullable', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'availability' => ['required', 'boolean'],
        ]);

        $data = $this->normalizeUnitPrices($data, $request);
        $data['image'] = $request->file('image')->store('items', 'public');
        $data['vendor_id'] = $this->vendor()->id;

        Item::create($data);

        return redirect()
            ->route('vendor.items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $this->authorizeItem($item);

        return view('vendor.items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorizeItem($item);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_jam' => ['nullable', 'numeric', 'min:0'],
            'price_bulan' => ['nullable', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'availability' => ['required', 'boolean'],
        ]);

        $data = $this->normalizeUnitPrices($data, $request);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($item->image);
            $data['image'] = $request->file('image')->store('items', 'public');
        } else {
            unset($data['image']);
        }

        $item->update($data);

        return redirect()
            ->route('vendor.items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $this->authorizeItem($item);

        Storage::disk('public')->delete($item->image);
        $item->delete();

        return redirect()
            ->route('vendor.items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Toggle availability (quick action from dashboard).
     */
    public function toggleAvailability(Item $item)
    {
        $this->authorizeItem($item);
        $item->update(['availability' => ! $item->availability]);

        return back()->with('success', 'Ketersediaan barang diperbarui.');
    }

    private function authorizeItem(Item $item): void
    {
        abort_if($item->vendor_id !== $this->vendor()->id, 403);
    }

    /**
     * Coerce blank optional unit prices to null so an empty field clears the
     * price (and hides that unit) instead of storing 0.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeUnitPrices(array $data, Request $request): array
    {
        foreach (['price_jam', 'price_bulan'] as $field) {
            $data[$field] = $request->filled($field) ? $data[$field] : null;
        }

        return $data;
    }
}
