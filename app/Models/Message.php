<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'body',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    // ── Sender Constants ───────────────────────────────────────────────────────

    const SENDER_USER   = 'user';
    const SENDER_VENDOR = 'vendor';

    // ── Relationships ──────────────────────────────────────────────────────────

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Polymorphic-ish sender lookup based on sender_type.
     */
    public function sender()
    {
        return $this->sender_type === self::SENDER_VENDOR
            ? Vendor::find($this->sender_id)
            : User::find($this->sender_id);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function fromUser(): bool
    {
        return $this->sender_type === self::SENDER_USER;
    }

    public function fromVendor(): bool
    {
        return $this->sender_type === self::SENDER_VENDOR;
    }
}
