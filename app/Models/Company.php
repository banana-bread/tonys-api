<?php

namespace App\Models;

use App\Traits\HasUuid;

class Company extends BaseModel
{
    use HasUuid;

    protected $appends = [];

    protected $visible = [
        'name',
        'address',
        'phone',
        'time_slot_duration',
        'booking_cancellation_period',
        'clients',
    ];

    public function clients() 
    {
        return $this->belongsToMany(Client::class, 'companies_clients');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function service_definitions()
    {
        return $this->hasMany(ServiceDefinition::class);
    }
}
