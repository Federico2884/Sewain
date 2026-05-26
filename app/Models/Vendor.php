<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Vendor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'vendor';

    protected $fillable = [
        'name',
        'email',
        'password',
        'ktp',
        'address',
        'total_star',
        'reviewer_count',
        'rating',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'rating'   => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(Item::class);
    }

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
}
