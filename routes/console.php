<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * ── Scheduler ──────────────────────────────────────────────────────────────
 * Sends rental due-soon & overdue reminders every day at 08:00.
 * Make sure your server cron is running:
 *   * * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1
 */
Schedule::command('sewain:send-reminders')->dailyAt('08:00');
