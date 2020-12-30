<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $visible = [
        'id',
        'service_definition_id',
        'booking_id'
    ];

    public function service_definition()
    {
        return $this->belongsTo(ServiceDefinition::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
