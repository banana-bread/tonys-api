<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;


class Employee extends Model
{
    use HasFactory, UuidTrait;

    protected $with = ['user'];

    protected $fillable = [
        'user_id',
        'admin'
    ];

    protected $casts = ['admin' => 'boolean'];

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
}
