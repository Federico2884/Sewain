<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentalOverdueNotification extends Notification
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

        return (new MailMessage)
            ->subject('⚠️ Pengembalian Barang Terlambat!')
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Sewa barang **{$this->rental->item->name}** sudah melewati batas waktu pengembalian ({$returnDate}).")
            ->line('Keterlambatan dapat mempengaruhi deposit dan rating Anda.')
            ->action('Lihat Detail Rental', route('user.rentals.show', $this->rental));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'rental_id'   => $this->rental->id,
            'item_name'   => $this->rental->item->name,
            'return_date' => $this->rental->expectedReturnDate()->toDateString(),
            'type'        => 'overdue',
        ];
    }
}
