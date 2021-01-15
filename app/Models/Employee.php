<?php

namespace App\Models;

use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

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
        'time_slots',
        'bookings',

        'past_bookings',
        'future_bookings'
    ];

    protected $casts = [
        'admin' => 'boolean'
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

    public function time_slots()
    {
        return $this->hasMany(TimeSlot::class);
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
