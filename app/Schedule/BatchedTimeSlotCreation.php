<?php

namespace App\Schedule;

use App\Jobs\CreateTimeSlots;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;

class BatchedTimeSlotCreation 
{
  // TODO: would like to do this with __invoke but doesn't work :(
  public function dispatch()
  {
    $employeeIdsToCreateSlotsFor = $this->_getEmployeeIds();
    $createSlotsJobBatches = $this->_createJobBatches($employeeIdsToCreateSlotsFor);
    $this->_dispatchJobBus($createSlotsJobBatches);
  }

  private function _getEmployeeIds(): Collection
  {
    return TimeSlot::whereNotExists(function($query) {
      $query->select('employee_id')
        ->from('time_slots')
        ->where('start_time', '>', now()->addDays(90));
    })
    ->groupBy('employee_id')
    ->pluck('employee_id');
  }

  private function _createJobBatches(Collection $employeeIds): Collection
  {
    return $employeeIds
      ->chunk(5)
      ->map(fn($ids) => new CreateTimeSlots($ids));
  }

  private function _dispatchJobBus(Collection $jobs)
  {
    Bus::batch($jobs)
      ->then(fn (Batch $batch) => logger('New time slots created!'))
      ->catch(fn (Batch $batch, \Throwable $e) => logger($e))
      ->dispatch();
  }
}
