<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    private function vendor()
    {
        return Auth::guard('vendor')->user();
    }

    public function index()
    {
        $rentals = $this->vendor()
            ->rentals()
            ->with(['user', 'item', 'returning'])
            ->latest()
            ->paginate(15);

        return view('vendor.rentals.index', compact('rentals'));
    }

    public function show(Rental $rental)
    {
        $this->authorizeRental($rental);
        $rental->load('user', 'item', 'returning', 'reviews', 'payment');

        return view('vendor.rentals.show', compact('rental'));
    }

    private function authorizeRental(Rental $rental): void
    {
        abort_if($rental->vendor_id !== $this->vendor()->id, 403);
    }
}
