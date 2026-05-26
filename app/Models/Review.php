<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'rating_to',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating'    => 'integer',
            'rating_to' => 'string',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    // ── Direction Helpers ──────────────────────────────────────────────────────

    /** Review written by user about the vendor */
    public function scopeForVendor($query)
    {
        return $query->where('rating_to', 'user_to_vendor');
    }

    /** Review written by vendor about the user/renter */
    public function scopeForUser($query)
    {
        return $query->where('rating_to', 'vendor_to_user');
    }
}
