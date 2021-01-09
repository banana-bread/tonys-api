<?php

namespace App\Models;

class Booking extends BaseModel
{
    protected $fillable = [
        'client_id', 
        'employee_id', 
        'started_at',
        'ended_at',
        'overridden',
    ];

    protected $visible = [
        'id',
        'client_id',
        'employee_id',
        'overridden',
        'started_at',
        'ended_at',

        'client',
        'employee',
        'services',

        'available'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'overridden' => 'boolean'
    ];

    protected $rules = [
        'client_id'     => 'nullable|string|max:36',
        'employee_id'   => 'required|string|max:36',
        // 'overridden'    => 'required|boolean',
        'started_at'    => 'date|before:ended_at',
        'ended_at'      => 'date|after:started_at'
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

    public function getAvailableAttribute()
    {
        return !$this->overridden && !$this->client_id;
    }
}
