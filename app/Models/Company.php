<?php

namespace App\Models;

use App\Helpers\BaseSchedule;
use App\Traits\HasUuid;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends BaseModel
{
    use HasUuid, SoftDeletes;

    protected $with = [];
    
    protected $appends = [];

    protected $visible = [
        'id',
        'name',
        'slug',
        'city',
        'region',
        'postal_code',
        'address',
        'country',
        'phone',
        'time_slot_duration',
        'booking_grace_period',
        'settings',
        'timezone',

        'clients',
        'employees',
        'owner',
        'service_definitions',

        'formatted_phone',
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
        return $this->hasMany(Employee::class)->orderBy('ordinal_position');
    }

    public function owner()
    {
        return $this->hasOne(Employee::class)->where('owner', true);
    }

    public function service_definitions()
    {
        return $this->hasMany(ServiceDefinition::class)->orderBy('ordinal_position');
    }

    // HELPERS
    
    public function slotsRequiredFor(int $bookingDuration)
    {
        return ceil($bookingDuration / $this->time_slot_duration);
    }

    // Custom attributes

    public function getFormattedPhoneAttribute(): string
    {
        return '('.Str::substr($this->phone, 0, 3).') '.Str::substr($this->phone, 3, 3).'-'.Str::substr($this->phone, 6);
    }

    // public function toArray(): array
    // {
    //     return array_merge(
    //         parent::toArray(),
    //         ['phone' => Str::substr($this->phone, 2)]
    //     );
    // }
}
