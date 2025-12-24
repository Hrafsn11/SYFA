<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cek pengembalian dana jatuh tempo setiap hari jam 09:00
        $schedule->command('sfinlog:check-pengembalian-jatuh-tempo')
            ->dailyAt('09:00')
            ->timezone('Asia/Jakarta');

        // Cek pengembalian dana SFinance jatuh tempo setiap hari jam 09:00
        $schedule->command('sfinance:check-pengembalian-jatuh-tempo')
            ->dailyAt('09:00')
            ->timezone('Asia/Jakarta');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
