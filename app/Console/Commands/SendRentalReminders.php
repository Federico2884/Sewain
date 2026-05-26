<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RentalDueSoonNotification;
use App\Notifications\RentalOverdueNotification;

class SendRentalReminders extends Command
{
    protected $signature   = 'sewain:send-reminders';
    protected $description = 'Kirim notifikasi pengingat untuk rental yang akan segera berakhir atau sudah lewat jatuh tempo.';

    public function handle(): int
    {
        $activeRentals = Rental::active()
            ->with(['user', 'item', 'vendor'])
            ->get();

        $dueSoon  = 0;
        $overdue  = 0;

        foreach ($activeRentals as $rental) {
            if ($rental->isOverdue()) {
                // Notify both user and vendor
                $rental->user->notify(new RentalOverdueNotification($rental));
                $rental->vendor->notify(new RentalOverdueNotification($rental));
                $overdue++;
            } elseif ($rental->isDueSoon(2)) {
                $rental->user->notify(new RentalDueSoonNotification($rental));
                $dueSoon++;
            }
        }

        $this->info("Selesai. Due soon: {$dueSoon}, Overdue: {$overdue}");

        return self::SUCCESS;
    }
}
