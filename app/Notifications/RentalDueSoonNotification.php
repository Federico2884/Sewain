<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentalDueSoonNotification extends Notification
{
    use Queueable;

    public function __construct(public Rental $rental) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $returnDate = $this->rental->expectedReturnDate()->translatedFormat('d F Y');
        $days       = $this->rental->daysRemaining();

        return (new MailMessage)
            ->subject('Pengingat: Pengembalian Barang Segera')
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Sewa barang **{$this->rental->item->name}** akan berakhir dalam {$days} hari ({$returnDate}).")
            ->line('Pastikan Anda mengembalikan barang tepat waktu agar deposit dikembalikan penuh.')
            ->action('Lihat Detail Rental', route('user.rentals.show', $this->rental));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'rental_id'   => $this->rental->id,
            'item_name'   => $this->rental->item->name,
            'return_date' => $this->rental->expectedReturnDate()->toDateString(),
            'type'        => 'due_soon',
        ];
    }
}
