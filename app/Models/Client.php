<?php

namespace App\Models;

use App\Traits\HasUuid;

class Client extends BaseModel
{
    use HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $with = ['user'];

    protected $fillable = [
        'id',
        'user_id'
    ];

    protected $visible = [
        'id',
        'user_id',

        'user'
    ];

    protected $rules = [];

    // Relations

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
