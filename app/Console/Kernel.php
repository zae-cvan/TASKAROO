<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // dito natin ilalagay ang command class kung gusto
        Commands\SendTaskDeadlineNotifications::class,
        Commands\AutoArchiveTasks::class, // ✅ Add this
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('notify:task-deadlines')->everyMinute();
        $schedule->command('tasks:auto-archive')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
