<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(private RatingService $ratingService) {}

    public function create(Rental $rental)
    {
        abort_if($rental->user_id !== Auth::id(), 403);
        abort_unless($rental->returning?->verified, 403, 'Rental belum selesai.');
        abort_if($rental->vendorReview()->exists(), 403, 'Sudah memberikan review.');

        return view('user.reviews.create', compact('rental'));
    }

    public function store(Request $request, Rental $rental)
    {
        abort_if($rental->user_id !== Auth::id(), 403);
        abort_unless($rental->returning?->verified, 403);
        abort_if($rental->vendorReview()->exists(), 403);

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $this->ratingService->addReview($rental, 'user_to_vendor', $data['rating'], $data['comment'] ?? null);

        return redirect()
            ->route('user.rentals.show', $rental)
            ->with('success', 'Review berhasil dikirim!');
    }
}
