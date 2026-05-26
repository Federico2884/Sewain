<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'no_telp',
        'total_star',
        'reviewer_count',
        'rating',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'dob'               => 'date',
            'rating'            => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function activeRentals()
    {
        return $this->hasMany(Rental::class)
            ->whereDoesntHave('returning', fn ($q) => $q->where('verified', true));
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function recalculateRating(): void
    {
        if ($this->reviewer_count > 0) {
            $this->rating = round($this->total_star / $this->reviewer_count, 2);
            $this->save();
        }
    }

    /**
     * Deposit multiplier based on user rating.
     * New users (no rating) pay full deposit.
     * High-rated users get a discount.
     */
    public function depositMultiplier(): float
    {
        if ($this->reviewer_count === 0) {
            return 1.0;
        }

        return match (true) {
            $this->rating >= 4.5 => 0.5,
            $this->rating >= 3.5 => 0.75,
            $this->rating >= 2.5 => 1.0,
            default              => 1.25,
        };
    }
}
