<?php

namespace App\Models;

use App\Traits\HasUuid;

class Company extends BaseModel
{
    use HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'company';

    protected $appends = [];

    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    protected $visible = [
        'name',
        'address',
        'phone',
        'time_slot_duration',
        'booking_cancellation_period',
    ];

    public static function booking_cancellation_period(): int
    {
        return DB::table('company')->first()->booking_cancellation_period;
    }
}
