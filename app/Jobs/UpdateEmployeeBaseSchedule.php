<?php

namespace App\Jobs;

use App\Helpers\BaseSchedule;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateEmployeeBaseSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Employee $employee;
    public BaseSchedule $updatedBaseSchedule;

    public function __construct(Employee $employee, BaseSchedule $updatedBaseSchedule)
    {
        $this->employee = $employee;
        $this->updatedBaseSchedule = $updatedBaseSchedule;
    }

    public function handle()
    {
        $this->employee->createSlotsAfterScheduleUpdate();
    }
}
