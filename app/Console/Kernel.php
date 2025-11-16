<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\UpdatePermissions::class,
        Commands\CreateRolePermission::class,
        Commands\CheckExpiredSubscriptions::class,
    ];
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Check for expired subscriptions every hour
        $schedule->command('subscriptions:check-expired')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();
        
        // Send daily reminder for subscriptions expiring soon
        $schedule->command('subscriptions:check-expired')
                 ->dailyAt('09:00')
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
