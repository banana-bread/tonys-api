<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TimeSlot extends BaseModel
{
    public $incrementing = true;
    protected $keyType = 'int';

    protected $visible = [
        'id',
        'employee_id',
        'company_id',
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

    // RELATIONS

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // HELPERS

    public function getNextSlots(int $totalSlotsRequired): Collection
    {
        return TimeSlot::where('start_time', '>', $this->start_time)
            ->where('employee_id', $this->employee_id)
            ->whereRaw('DATE(?) = DATE(start_time)', [$this->start_time])
            ->orderBy('start_time')
            ->take($totalSlotsRequired - 1)
            ->get();
    }

    public static function lockAndReserve($slots)
    {
        $ids = $slots instanceof TimeSlot
            ? [$slots->id]
            : $slots->pluck('id');

        static::whereIn('id', $ids)
            ->lockForUpdate()
            ->update(['reserved' => true]);
    }

    public static function isReserved($slots): bool
    {
        return $slots instanceof TimeSlot
            ? $slots->reserved
            : $slots->contains(function ($slot) { return $slot->reserved; });
    }
}
