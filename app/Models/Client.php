<?php

namespace App\Models;

use App\Traits\HasUuid;

class Client extends BaseModel
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
        'user_id'
    ];

    protected $visible = [
        'id',
        'name',
        'phone',
        'email'
    ];

    // Relations

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
