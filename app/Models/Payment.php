<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'amount',
        'rent_amount',
        'deposit_amount',
        'status',
        'method',
        'payment_proof',
        'transaction_id',
        'paid_at',
        'failed_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'         => 'decimal:2',
            'rent_amount'    => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'paid_at'        => 'datetime',
            'failed_at'      => 'datetime',
            'refunded_at'    => 'datetime',
        ];
    }

    // ── Status Constants ───────────────────────────────────────────────────────

    const STATUS_PENDING               = 'pending';                // belum bayar
    const STATUS_AWAITING_CONFIRMATION = 'awaiting_confirmation';  // bukti diunggah
    const STATUS_PAID                  = 'paid';                   // dikonfirmasi
    const STATUS_FAILED                = 'failed';                 // gagal
    const STATUS_REFUNDED              = 'refunded';               // dikembalikan

    // ── Method Constants ───────────────────────────────────────────────────────

    const METHOD_GOPAY      = 'gopay';
    const METHOD_OVO        = 'ovo';
    const METHOD_DANA       = 'dana';
    const METHOD_SHOPEEPAY  = 'shopeepay';
    const METHOD_BCA        = 'bca';
    const METHOD_MANDIRI    = 'mandiri';

    public static function methods(): array
    {
        return [
            self::METHOD_GOPAY     => ['label' => 'GoPay',     'group' => 'E-Wallet', 'color' => 'bg-emerald-500'],
            self::METHOD_OVO       => ['label' => 'OVO',       'group' => 'E-Wallet', 'color' => 'bg-purple-600'],
            self::METHOD_DANA      => ['label' => 'DANA',      'group' => 'E-Wallet', 'color' => 'bg-sky-500'],
            self::METHOD_SHOPEEPAY => ['label' => 'ShopeePay', 'group' => 'E-Wallet', 'color' => 'bg-orange-500'],
            self::METHOD_BCA       => ['label' => 'BCA Virtual Account',     'group' => 'Bank Transfer', 'color' => 'bg-blue-700'],
            self::METHOD_MANDIRI   => ['label' => 'Mandiri Virtual Account', 'group' => 'Bank Transfer', 'color' => 'bg-yellow-500'],
        ];
    }

    public function methodLabel(): ?string
    {
        return self::methods()[$this->method]['label'] ?? $this->method;
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAwaitingConfirmation(): bool
    {
        return $this->status === self::STATUS_AWAITING_CONFIRMATION;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING               => 'Menunggu Pembayaran',
            self::STATUS_AWAITING_CONFIRMATION => 'Menunggu Konfirmasi',
            self::STATUS_PAID                  => 'Lunas',
            self::STATUS_FAILED                => 'Gagal',
            self::STATUS_REFUNDED              => 'Dikembalikan',
            default                            => $this->status,
        };
    }
}
