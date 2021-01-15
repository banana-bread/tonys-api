<?php

namespace App\Models;

use Illuminate\Support\Collection;

class TimeSlot extends BaseModel
{
    protected $fillable = [
        'employee_id', 
        'start_time',
        'end_time',
        'overridden',
    ];

    protected $visible = [
        'id',
        'employee_id',
        'reserved',
        'start_time',
        'end_time',

        'employee',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'reserved'   => 'boolean'
    ];

    // Relations

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getNextSlots(int $numberOfSlotsRequired): Collection
    {
        return TimeSlot::where('start_time', '>', $this->start_time)
            ->where('employee_id', $this->employee_id)
            ->whereRaw('DATE(?) = DATE(start_time)', [$this->start_time])
            ->orderBy('start_time')
            ->take($numberOfSlotsRequired - 1)
            ->get();
    }
}
