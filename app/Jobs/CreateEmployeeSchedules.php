<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateEmployeeSchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Employee $employee;
    public int $days;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, int $days)
    {
        $this->employee = $employee;
        $this->days = $days;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $schedules = $employee->createSchedulesForNext($this->days);

        $schedules->each(function ($schedule) {
            $schedule->createTimeSlots();
        });
    }
}
