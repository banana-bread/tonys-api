<?php

namespace App\Models;

class Booking extends BaseModel
{
    protected $fillable = [
        'client_id',
        'employee_id', 
        'cancelled_at',
        'cancelled_by',
        'started_at',
        'ended_at',
    ];

    protected $visible = [
        'id',
        'client_id',
        'employee_id',
        'cancelled_at',
        'cancelled_by',
        'started_at',
        'ended_at',

        'client',
        'employee',
        'services',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    // Relations

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
