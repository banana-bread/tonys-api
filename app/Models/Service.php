<?php

namespace App\Models;

use App\Traits\HasUuid;

class Service extends BaseModel
{
    use HasUuid;
    
    protected $visible = [
        'id',
        'name',
        'description',
        'price',
        'duration',
        'service_definition_id',
        'booking_id',
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
}
