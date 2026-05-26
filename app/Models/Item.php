<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'category',
        'price',
        'deposit',
        'image',
        'availability',
    ];

    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'deposit'      => 'decimal:2',
            'availability' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Dates when the item is already booked (start → end).
     * Hanya rental yang sudah lunas DAN belum dikembalikan yang mengunci tanggal.
     * Rental dengan payment masih pending tidak menyebabkan tanggal terblok.
     */
    public function bookedDates(): array
    {
        return $this->rentals()
            ->whereHas('payment', fn ($q) => $q->where('status', 'paid'))
            ->whereDoesntHave('returning', fn ($q) => $q->where('verified', true))
            ->get()
            ->map(function (Rental $rental) {
                $start = $rental->start;
                $end   = $rental->expectedReturnDate();

                $dates = [];
                $cursor = $start->copy();
                while ($cursor->lte($end)) {
                    $dates[] = $cursor->toDateString();
                    $cursor->addDay();
                }

                return $dates;
            })
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Rental aktif (sudah lunas DAN belum dikembalikan) untuk item ini.
     */
    public function activeRentals()
    {
        return $this->rentals()
            ->whereHas('payment', fn ($q) => $q->where('status', 'paid'))
            ->whereDoesntHave('returning', fn ($q) => $q->where('verified', true))
            ->get();
    }

    /**
     * Tanggal kembali paling akhir dari semua rental yang sedang aktif.
     * null jika tidak ada rental aktif.
     */
    public function nextAvailableFrom(): ?Carbon
    {
        $rentals = $this->activeRentals();

        if ($rentals->isEmpty()) {
            return null;
        }

        return $rentals
            ->map(fn (Rental $r) => $r->expectedReturnDate())
            ->sortDesc()
            ->first()
            ->copy()
            ->addDay()
            ->startOfDay();
    }

    /**
     * Apakah rentang sewa yang diajukan (start + duration + unit) overlap
     * dengan salah satu rental aktif?
     */
    public function overlapsBookedDates(Carbon $start, int $duration, string $unit): bool
    {
        $end = match ($unit) {
            'jam'   => $start->copy()->addHours($duration),
            'hari'  => $start->copy()->addDays($duration),
            'bulan' => $start->copy()->addMonths($duration),
            default => $start->copy()->addDays($duration),
        };

        return $this->activeRentals()
            ->contains(function (Rental $rental) use ($start, $end) {
                $otherStart = $rental->start;
                $otherEnd   = $rental->expectedReturnDate();

                return ! ($end->lt($otherStart) || $start->gt($otherEnd));
            });
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('availability', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
