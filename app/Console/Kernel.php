<?php

namespace App\Console;

use App\Schedule\BatchedTimeSlotCreation;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->_purgeOldSlots($schedule);
        $this->_createNewSlots($schedule);
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

    private function _purgeOldSlots(Schedule $schedule)
    {
      $schedule->command('slots:purge')
        ->monthlyOn(1, '03:00')
        ->environments(['production']);
    }

    private function _createNewSlots(Schedule $schedule)
    {
      $schedule->call(fn() => (new BatchedTimeSlotCreation)->dispatch())
        ->monthlyOn(1, '03:00')
        ->environments(['production']);
    }
}
