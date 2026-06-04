<?php

namespace App\Http\Controllers;

use App\Models\Item;

class HomeController extends Controller
{
    /**
     * Landing page: hero, category shortcuts, and a few featured items.
     */
    public function __invoke()
    {
        $featuredItems = Item::with('vendor')
            ->available()
            ->latest()
            ->take(8)
            ->get();

        $categories = Item::CATEGORIES;

        return view('welcome', compact('featuredItems', 'categories'));
    }
}
