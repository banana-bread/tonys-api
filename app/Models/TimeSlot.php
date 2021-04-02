<?php

namespace App\Models;

use Illuminate\Support\Collection;

class TimeSlot extends BaseModel
{
    public $incrementing = true;
    protected $keyType = 'int';

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

    public function getNextSlots(int $totalSlotsRequired): Collection
    {
        return TimeSlot::where('start_time', '>', $this->start_time)
            ->where('employee_id', $this->employee_id)
            ->whereRaw('DATE(?) = DATE(start_time)', [$this->start_time])
            ->orderBy('start_time')
            ->take($totalSlotsRequired - 1)
            ->get();
    }
}
