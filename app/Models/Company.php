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

    // CUSTOM ATTRIBUTES

    public function getBaseScheduleAttribute()
    {
        return new BaseSchedule($this->settings['base_schedule']);
    }

    public function setBaseScheduleAttribute()
    {
        $updatedSettings = $this->settings;
        $updatedSettings['base_schedule'] = $schedule->toArray();

        $this->attributes['settings'] = json_encode($updatedSettings);
    }

    // HELPERS
    
    public function slotsRequiredFor(int $duration)
    {
        return ceil($duration / $this->time_slot_duration);
    }

    // ACTIONS

    public function updateBaseSchedule(BaseSchedule $newBaseSchedule)
    {
        // auth()->user()->employee()->company()->employees->each(function ($employee) use ($newBaseSchedule) {
        //     if (! $employee->base_schedule->fallsWithin($newBaseSchedule))
        //     {
        //         throw new Exception('All employees hours must fall within new schedule');
        //     }
        // });

        $this->base_schedule = $newBaseSchedule;
        $this->save();
    }
}
