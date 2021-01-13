<?php

namespace App\Models;

class Booking extends BaseModel
{
    protected $fillable = [
        'employee_id', 
        'started_at',
        'ended_at',
        'overridden',
    ];

    protected $visible = [
        'id',
        'employee_id',
        'reserved',
        'started_at',
        'ended_at',

        'employee',
        'client',
        'services',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'reserved'   => 'boolean'
    ];
    // TODO: need to rethink the usefulness of this watson validating package.
    protected $rules = [
    //     'employee_id'   => 'required|string|max:36',
    //     // 'overridden'    => 'required|boolean',
    //     'started_at'    => 'date|before:ended_at',
    //     'ended_at'      => 'date|after:started_at'
    ];

    // Relations

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
