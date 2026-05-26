<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'item_id',
        'duration',
        'unit',
        'start',
        'method',
    ];

    protected function casts(): array
    {
        return [
            'start'    => 'date',
            'duration' => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function returning()
    {
        return $this->hasOne(Returning::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function userReview()
    {
        return $this->hasOne(Review::class)->where('rating_to', 'vendor_to_user');
    }

    public function vendorReview()
    {
        return $this->hasOne(Review::class)->where('rating_to', 'user_to_vendor');
    }

    // ── Computed Helpers ───────────────────────────────────────────────────────

    /**
     * Calculate the expected return date based on unit and duration.
     */
    public function expectedReturnDate(): Carbon
    {
        $start = $this->start->copy();

        return match ($this->unit) {
            'jam'   => $start->addHours($this->duration),
            'hari'  => $start->addDays($this->duration),
            'bulan' => $start->addMonths($this->duration),
            default => $start->addDays($this->duration),
        };
    }

    /**
     * Days remaining until expected return.
     */
    public function daysRemaining(): int
    {
        return (int) now()->diffInDays($this->expectedReturnDate(), false);
    }

    /**
     * Total rental cost (price × duration).
     */
    public function totalCost(): float
    {
        return (float) ($this->item->price * $this->duration);
    }

    /**
     * Deposit adjusted by user rating multiplier.
     */
    public function adjustedDeposit(): float
    {
        return (float) ($this->item->deposit * $this->user->depositMultiplier());
    }

    /**
     * Grand total including adjusted deposit.
     */
    public function grandTotal(): float
    {
        return $this->totalCost() + $this->adjustedDeposit();
    }

    // ── Status Helpers ─────────────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->payment?->isPaid() === true;
    }

    public function isPending(): bool
    {
        return $this->payment?->isPending() === true;
    }

    public function isFailed(): bool
    {
        return $this->payment?->isFailed() === true;
    }

    /**
     * Active = sudah dibayar DAN belum dikembalikan (terverifikasi).
     */
    public function isActive(): bool
    {
        return $this->isPaid() && (! $this->returning || ! $this->returning->verified);
    }

    public function isOverdue(): bool
    {
        return $this->isActive() && now()->isAfter($this->expectedReturnDate());
    }

    public function isDueSoon(int $days = 2): bool
    {
        $remaining = $this->daysRemaining();

        return $this->isActive() && $remaining >= 0 && $remaining <= $days;
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereDoesntHave('returning', fn ($q) => $q->where('verified', true));
    }

    public function scopeCompleted($query)
    {
        return $query->whereHas('returning', fn ($q) => $q->where('verified', true));
    }

    public function scopeDueSoon($query, int $days = 2)
    {
        return $query->active()
            ->whereDate('start', '<=', now())
            ->get()
            ->filter(fn ($r) => $r->isDueSoon($days));
    }
}
