<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateEmployeeTimeSlots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Employee $employee;
    public int $days;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, int $days, int $starting = 0)
    {
        $this->employee = $employee;
        $this->days = $days;
        $this->starting = $starting;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->employee->createTimeSlotsForNext($this->days, $this->starting);
    }
}
