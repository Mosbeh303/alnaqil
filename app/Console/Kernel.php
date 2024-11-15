<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --stop-when-empty')
             ->everyMinute()
             ->withoutOverlapping();

        $schedule->command('token:refresh')->daily(); // salla token
        $schedule->command('zidtoken:refresh')->daily();

        $schedule->command('createInvoices:daily')->daily();
        $schedule->command('createInvoices:weekly')->weekly()->fridays()->at('00:00');;
        $schedule->command('createInvoices:monthly')->monthly();
        $schedule->command('createInvoices:quarterly')->quarterly();
        $schedule->command('createInvoices:annualy')->yearly();
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
