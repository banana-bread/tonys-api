<?php

namespace App\Models;

use App\Traits\HasUuid;

class Employee extends BaseModel
{
    use HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $appends = [
        'name',
        'phone',
        'email'
    ];

    protected $fillable = [
        'id',
        'user_id',
        'admin'
    ];

    protected $visible = [
        'id',
        'name',
        'phone',
        'email',
        'admin'
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

    public function getNameAttribute()
    {
        return $this->user->name;
    }

    public function getPhoneAttribute()
    {
        return $this->user->phone;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    // Custom Relations
    // TODO: test
    public function getPastBookingsAttribute()
    {
        return $this->hasMany(Booking::class)
            ->where('started_at', '<', now());
    }

    // TODO: test
    public function getFutureBookingsAttribute()
    {
        return $this->hasMany(Booking::class)
            ->where('started_at', '>', now());
    }

}
