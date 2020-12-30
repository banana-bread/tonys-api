<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

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

    public function employee()
    {
        $this->belongsTo(Employee::class);
    }
}
