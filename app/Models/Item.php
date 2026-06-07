<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * Curated marketplace categories — the single source of truth used by the
     * landing page and catalogue. Some categories may not have items yet; they
     * still appear so the marketplace feels complete and ready to grow.
     *
     * @var array<string, string> Category name => emoji icon
     */
    public const CATEGORIES = [
        'Sepeda Motor' => '🏍️',
        'Mobil' => '🚗',
        'Kamera' => '📷',
        'Elektronik' => '💻',
        'Alat Kemah' => '⛺',
        'Perkakas' => '🔧',
        'Alat Musik' => '🎸',
        'Sepeda' => '🚲',
    ];

    /**
     * Emoji icon for a category name, with a sensible fallback.
     */
    public static function categoryIcon(?string $category): string
    {
        foreach (self::CATEGORIES as $name => $icon) {
            if (strcasecmp($name, (string) $category) === 0) {
                return $icon;
            }
        }

        return '📦';
    }

    /**
     * Rental units, in display order. `hari` is always offered; `jam` and
     * `bulan` are only offered when the vendor sets a price for them.
     *
     * @var array<string, string> Unit key => price column
     */
    public const UNIT_PRICE_COLUMNS = [
        'jam' => 'price_jam',
        'hari' => 'price',
        'bulan' => 'price_bulan',
    ];

    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'category',
        'price',
        'price_jam',
        'price_bulan',
        'deposit',
        'image',
        'availability',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_jam' => 'decimal:2',
            'price_bulan' => 'decimal:2',
            'deposit' => 'decimal:2',
            'availability' => 'boolean',
        ];
    }

    // ── Pricing ────────────────────────────────────────────────────────────────

    /**
     * Price for a single unit of the given rental unit. Falls back to the
     * daily price when the requested unit has no vendor-set price.
     */
    public function priceFor(string $unit): float
    {
        $column = self::UNIT_PRICE_COLUMNS[$unit] ?? 'price';

        return (float) ($this->{$column} ?? $this->price);
    }

    /**
     * Units the vendor actually offers, mapped to their price, in display
     * order. `hari` is always present; `jam`/`bulan` only when priced.
     *
     * @return array<string, float>
     */
    public function offeredUnitPrices(): array
    {
        $prices = [];

        foreach (self::UNIT_PRICE_COLUMNS as $unit => $column) {
            if ($unit === 'hari' || $this->{$column} !== null) {
                $prices[$unit] = (float) $this->{$column};
            }
        }

        return $prices;
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
                $end = $rental->expectedReturnDate();

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
            'jam' => $start->copy()->addHours($duration),
            'hari' => $start->copy()->addDays($duration),
            'bulan' => $start->copy()->addMonths($duration),
            default => $start->copy()->addDays($duration),
        };

        return $this->activeRentals()
            ->contains(function (Rental $rental) use ($start, $end) {
                $otherStart = $rental->start;
                $otherEnd = $rental->expectedReturnDate();

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
