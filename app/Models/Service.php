<?php

namespace App\Models;

use App\Traits\HasUuid;

class Service extends BaseModel
{
    use HasUuid;

    protected $appends = [
        'name',
        'price',
        'duration',
    ];
    
    protected $visible = [
        'id',
        'service_definition_id',
        'booking_id',

        'name',
        'price',
        'duration',
    ];

    // RELATIONS
    
    public function service_definition()
    {
        return $this->belongsTo(ServiceDefinition::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // CUSTOM ATTRIBUTES

    public function getNameAttribute()
    {
        return $this->service_definition->name;
    }

    public function getPriceAttribute()
    {
        return $this->service_definition->price;
    }

    public function getDurationAttribute()
    {
        return $this->service_definition->price;
    }
}
