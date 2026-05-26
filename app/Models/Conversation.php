<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
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

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Find or create the conversation between a user & vendor.
     */
    public static function between(int $userId, int $vendorId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'vendor_id' => $vendorId],
        );
    }

    /**
     * Touch last_message_at when a new message arrives.
     */
    public function touchLastMessage(): void
    {
        $this->update(['last_message_at' => now()]);
    }

    /**
     * Count unread messages for the given participant ('user' or 'vendor').
     */
    public function unreadCountFor(string $participant): int
    {
        $opposite = $participant === 'user' ? Message::SENDER_VENDOR : Message::SENDER_USER;

        return $this->messages()
            ->where('sender_type', $opposite)
            ->whereNull('read_at')
            ->count();
    }
}
