<?php

namespace App\Models;

class EmployeeSchedule extends BaseModel
{
    public $incrementing = true;
    protected $keyType = 'int';

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

    // RELATIONS

    public function employee()
    {
        $this->belongsTo(Employee::class);
    }
}
