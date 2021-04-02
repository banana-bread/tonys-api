<?php

namespace App\Models;

class Service extends BaseModel
{
    protected $visible = [
        'id',
        'service_definition_id',
        'booking_id'
    ];

    // Relations
    
    public function service_definition()
    {
        return $this->belongsTo(ServiceDefinition::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
