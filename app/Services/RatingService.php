<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Rental;

class RatingService
{
    /**
     * Add a review and update the target's aggregate rating.
     *
     * @param  Rental  $rental
     * @param  string  $direction  'user_to_vendor' | 'vendor_to_user'
     * @param  int     $stars      1–5
     * @param  string|null $comment
     */
    public function addReview(
        Rental $rental,
        string $direction,
        int $stars,
        ?string $comment = null
    ): Review {
        $review = Review::create([
            'rental_id' => $rental->id,
            'rating_to' => $direction,
            'rating'    => $stars,
            'comment'   => $comment,
        ]);

        // Update aggregate on the correct target
        if ($direction === 'user_to_vendor') {
            $target = $rental->vendor;
        } else {
            $target = $rental->user;
        }

        $target->increment('total_star', $stars);
        $target->increment('reviewer_count');
        $target->recalculateRating();

        return $review;
    }
}
