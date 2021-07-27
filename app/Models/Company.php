<?php

namespace App\Models;

use App\Helpers\BaseSchedule;
use App\Traits\HasUuid;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends BaseModel
{
    use HasUuid, SoftDeletes;

    protected $with = ['owner'];
    
    protected $appends = [];

    protected $visible = [
        'id',
        'name',
        'address',
        'phone',
        'time_slot_duration',
        'booking_grace_period',
        'settings',

        'clients',
        'employees',
        'owner',
    ];

    protected $casts = [
        'settings' => 'collection'
    ];

    // RELATIONS

    public function clients() 
    {
        return $this->hasMany(Client::class, 'companies_clients')->withTimestamps();
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function owner()
    {
        return $this->hasOne(Employee::class)->where('owner', true);
    }

    public function service_definitions()
    {
        return $this->hasMany(ServiceDefinition::class);
    }

    // HELPERS
    
    public function slotsRequiredFor(int $bookingDuration)
    {
        return ceil($bookingDuration / $this->time_slot_duration);
    }
}
