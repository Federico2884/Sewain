<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Returning extends Model
{
    /**
     * The returnings table uses rental_id as its primary key (1-to-1 with rentals).
     */
    protected $primaryKey = 'rental_id';

    public $incrementing = false;

    protected $fillable = [
        'rental_id',
        'method',
        'status',
        'verified',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    // ── Status Constants ───────────────────────────────────────────────────────

    const STATUS_PENDING  = 'pending';   // user submitted return request
    const STATUS_RETURNED = 'returned';  // item physically back with vendor
    const STATUS_DAMAGED  = 'damaged';   // vendor confirmed damage
    const STATUS_GOOD     = 'good';      // vendor confirmed good condition

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isGoodCondition(): bool
    {
        return $this->status === self::STATUS_GOOD;
    }

    public function isDamaged(): bool
    {
        return $this->status === self::STATUS_DAMAGED;
    }
}
