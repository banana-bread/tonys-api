<?php

namespace App\Models;

use Illuminate\Support\Collection;

class EmployeeSchedule extends BaseModel
{
    public $incrementing = true;
    protected $keyType = 'int';

    protected $visible = [
        'id',
        'employee_id',
        'start_time',
        'end_time',
        'weekend',
        'holiday',

        'employee',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'weekend' => 'boolean',
        'holiday' => 'boolean',
    ];

    // RELATIONS

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // HELPERS
    public function isWorkingDay(): bool
    {
        return !$this->weekend && !$this->holiday;
    }

    public function createTimeSlots(): Collection
    {
        if (! $this->isWorkingDay()) return null;

        $singleSlotDuration = $this->employee->company->time_slot_duration;
        $totalSecondsInWorkDay = $this->end_time->diffInSeconds($this->start_time);
        $totalSlotsInWorkDay = floor($totalSecondsInWorkDay / $singleSlotDuration); 

        $timeSlots = new Collection();

        for ($i = 0; $i < $totalSlotsInWorkDay; $i++)
        {
            $startTime = $this->start_time->copy()->addSeconds($i * $singleSlotDuration);
            $endTime = $startTime->copy()->addSeconds($singleSlotDuration);

            $timeSlots->push(
                TimeSlot::create([
                    'employee_id' => $this->employee_id,
                    'company_id' => $this->employee->company_id,
                    'reserved' => false,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ])
            ); 
        }

        return $timeSlots;
    }
}
