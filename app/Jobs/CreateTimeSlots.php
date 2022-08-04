<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CreateTimeSlots implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employeeIds;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $employeeIds)
    {
      $this->employeeIds = $employeeIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $employees = Employee::find($this->employeeIds);
        $employees->each(fn ($e) => $e->createSlotsForNext(150));
    }
}
