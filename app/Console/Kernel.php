<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands
     */
    protected $commands = [
        Commands\ProcessEmailQueueCommand::class,
        Commands\QueueStatusCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping()->runInBackground();
    
        // Clean up failed jobs older than 7 days
        $schedule->command('queue:prune-failed --hours=168')
            ->daily()
            ->at('02:00');

        // Restart queue workers daily to prevent memory leaks
        $schedule->command('queue:restart')
            ->dailyAt('03:00')
            ->environments(['production']);
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
