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
        // Hitung denda keterlambatan peminjaman Finlog setiap hari jam 08:00
        $schedule->command('sfinlog:calculate-late-penalty')
            ->dailyAt('08:00')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/late-penalty.log'));

        // Cek pengembalian dana jatuh tempo setiap hari jam 09:00
        $schedule->command('sfinlog:check-pengembalian-jatuh-tempo')
            ->dailyAt('09:00')
            ->timezone('Asia/Jakarta');

        // Cek pengembalian dana SFinance jatuh tempo setiap hari jam 09:00
        $schedule->command('sfinance:check-pengembalian-jatuh-tempo')
            ->dailyAt('09:00')
            ->timezone('Asia/Jakarta');

        // Hitung denda keterlambatan peminjaman SFinance setiap hari jam 08:05
        $schedule->command('sfinance:calculate-late-penalty')
            ->dailyAt('08:05')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/late-penalty-sfinance.log'));

        // Update lama pemakaian (masa penggunaan) SFinance setiap hari jam 08:10
        $schedule->command('sfinance:update-lama-pemakaian')
            ->dailyAt('08:10')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/lama-pemakaian-sfinance.log'));
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
