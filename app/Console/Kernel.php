<?php

namespace App\Console;

use App\Models\Employee;
use App\Models\TimeSlot;
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
        $this->purgeOldSlots($schedule);
        $this->createNewSlots($schedule);
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

    private function purgeOldSlots(Schedule $schedule)
    {
      $schedule->command('slots:purge')
            ->monthlyOn(1, '03:00')
            ->environments(['production']);
    }

    private function createNewSlots(Schedule $schedule)
    {
      // 1. schedule jobs monthly
      $schedule->call(function() 
      {
        // 2. get employee ids that we should create future time slots for
        $employeeIdsToCreateSlotsFor = 
          TimeSlot::whereNotExists(function($query) {
            $query->select('employee_id')
              ->from('time_slots')
              ->where('start_time', '>', now()->addDays(90));
          })
          ->groupBy('employee_id')
          ->pluck('employee_id');
        
        // 3. create batches of create time slot jobs, 5 employees per job.
        $createNewSlotsJobs = collect($employeeIdsToCreateSlotsFor)->chunk(5)->map(
          fn($employeeIds) => new CreateTimeSlots($employeeIds)
        );

        // 4. dispatch job bus
        Bus::Batch($createNewSlotsJobs)->dispatch();
      })
      ->monthlyOn(2, '03:00')
      ->environments(['production']);
    }
}
