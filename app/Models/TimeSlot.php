<?php

namespace App\Models;

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
    // TODO: need to rethink the usefulness of this watson validating package.
    protected $rules = [
    //     'employee_id'   => 'required|string|max:36',
    //     // 'overridden'    => 'required|boolean',
    //     'start_time'    => 'date|before:end_time',
    //     'end_time'      => 'date|after:start_time'
    ];

    // Relations

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
