<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
       Commands\MoqNotification::class,
       Commands\AddOpeningStocks::class,
       Commands\AddProductStocks::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('moq:day')
                  ->twiceDaily(9, 16);
        
        /*$schedule->command('openingbalance:day')
                  ->daily(12);*/
        $schedule->command('openingbalance:day')
                  ->dailyAt('00:00');
        $schedule->command('stockbalance:day')
                  ->dailyAt('16:15'); //00:15
        
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
