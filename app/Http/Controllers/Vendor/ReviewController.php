<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(private RatingService $ratingService) {}

    private function vendor()
    {
        return Auth::guard('vendor')->user();
    }

    public function create(Rental $rental)
    {
        $this->authorizeVendor($rental);
        abort_unless($rental->returning?->verified, 403, 'Rental belum selesai.');
        abort_if($rental->userReview()->exists(), 403, 'Sudah memberikan review.');

        return view('vendor.reviews.create', compact('rental'));
    }

    public function store(Request $request, Rental $rental)
    {
        $this->authorizeVendor($rental);
        abort_unless($rental->returning?->verified, 403);
        abort_if($rental->userReview()->exists(), 403);

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $this->ratingService->addReview($rental, 'vendor_to_user', $data['rating'], $data['comment'] ?? null);

        return redirect()
            ->route('vendor.rentals.show', $rental)
            ->with('success', 'Review penyewa berhasil dikirim!');
    }

    private function authorizeVendor(Rental $rental): void
    {
        abort_if($rental->vendor_id !== $this->vendor()->id, 403);
    }
}
