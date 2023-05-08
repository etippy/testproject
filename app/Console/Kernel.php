<?php

namespace App\Console;

use App\Console\Commands\ProcessTransactions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //ProcessTransactions::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // The commands provided within the Laravel framework...
        $schedule->command('inspire')->hourly();

        // Here, you can add your own scheduled commands if needed.
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
