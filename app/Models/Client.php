<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Client extends Model
{
    use HasFactory, UuidTrait;

    protected $with = ['user'];

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(Client::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
