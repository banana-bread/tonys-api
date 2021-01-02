<?php

namespace App\Models;

use App\Exceptions\ModelValidationException;
use App\Traits\HasUuid;
use Carbon\Carbon;

class Employee extends BaseModel
{
    use HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $with = [
        'user'
    ];

    protected $fillable = [
        'id',
        'user_id',
        'admin'
    ];

    protected $visible = [
        'id',
        'user_id',
        'admin',

        'user',
        'schedules',
        'bookings',

        'past_bookings',
        'future_bookings'
    ];

    protected $casts = [
        'admin' => 'boolean'
    ];

    protected $rules = [
        'user_id' => 'required|string',
        'admin'   => 'required|boolean'
    ];

    // Relations

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Custom Relations
    // TODO: test
    public function getPastBookingsAttribute()
    {
        return $this->hasMany(Booking::class)
            ->where('started_at', '<', Carbon::now());
    }

    // TODO: test
    public function getFutureBookingsAttribute()
    {
        return $this->hasMany(Booking::class)
            ->where('started_at', '>', Carbon::now());
    }
}
