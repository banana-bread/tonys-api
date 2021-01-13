<?php

namespace App\Models;

use App\Exceptions\ModelValidationException;
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

    public function isAvailableAt(Carbon $bookingStartedAt)
    {
        return $this->bookings()
                    ->where('started_at', $bookingStartedAt)
                    ->first()
                    ->available;
    }

    public function isAvailableBetween(Carbon $bookingPeriodStart, Carbon $bookingPeriodEnd)
    {
        $bookingsWithinPeriod = 
            $this->bookings()
                 ->whereBetween('started_at', [
                    $bookingPeriodStart, 
                    $bookingPeriodEnd
                 ])
                 ->get();
        // TODO: check what happens when nothing is returend from above query

        $isOverridden = $bookingsWithinPeriod->contains('overridden', true);
        $isReserved = $bookingsWithinPeriod->contains(function ($booking) {
            return !!$booking->client_id;
        });

        $isAvailable = !$isOverridden && !$isReserved;
        
        return $isAvailable;
    }


    public function hasFullAvailabilityIn(Collection $bookings)
    {
        $isOverridden = $bookings->contains('overridden', true);
        $isReserved = $bookings->contains(function ($booking) {
            return !!$booking->client_id;
        });

        $isAvailable = !$isOverridden && !$isReserved;
        
        return $isAvailable;
    }
}
