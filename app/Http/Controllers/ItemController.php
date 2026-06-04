<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Public catalogue of available items.
     */
    public function index(Request $request)
    {
        $query = Item::with('vendor')
            ->available();

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $items = $query->latest()->paginate(12)->withQueryString();

        // Canonical categories first, then any extras already present in the data.
        $categories = collect(array_keys(Item::CATEGORIES))
            ->merge(Item::query()->distinct()->pluck('category'))
            ->unique(fn (string $name) => mb_strtolower($name))
            ->values();

        return view('items.index', compact('items', 'categories'));
    }

    /**
     * Public item detail page with booked-dates calendar data.
     */
    public function show(Item $item)
    {
        $item->load('vendor');
        $bookedDates = $item->bookedDates();
        $nextAvailableFrom = $item->nextAvailableFrom();
        $isRentedNow = in_array(now()->toDateString(), $bookedDates, true);

        return view('items.show', compact('item', 'bookedDates', 'nextAvailableFrom', 'isRentedNow'));
    }
}
