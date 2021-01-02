<?php

namespace App\Models;

class EmployeeSchedule extends BaseModel
{
    protected $fillable = [
        'employee_id',
        'work_date',
        'start_time',
        'end_time',
        'weekend',
        'holiday'
    ];

    protected $visible = [
        'id',
        'employee_id',
        'work_date',
        'start_time',
        'end_time',
        'weekend',
        'holiday'
    ];

    protected $casts = [
        'work_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'weekend' => 'boolean',
        'holiday' => 'boolean',
    ];

    protected $rules = [];

    // Relations

    public function employee()
    {
        $this->belongsTo(Employee::class);
    }
}
