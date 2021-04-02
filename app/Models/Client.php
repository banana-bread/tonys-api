<?php

namespace App\Models;

use App\Exceptions\InvalidParameterException;
use App\Models\Interfaces\ReceivesBookingNotifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Traits\HasUuid;

class Client extends BaseModel implements ReceivesBookingNotifications
{
    use HasUuid;

    protected $appends = [
        'name',
        'phone',
        'email'
    ];

    protected $visible = [
        'id',
        'name',
        'phone',
        'email',
        'companies',
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

    public function companies()
    {
        return $this->hasMany(Company::class, 'companies_clients');
    }

    public function getNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getPhoneAttribute(): string
    {
        return $this->user->phone;
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }

    public function getSubscribesToEmailsAttributes(): bool
    {
        return $this->user->suscribed_to_emails;
    }

    public function isAvailableDuring($timeSlots): bool
    {
        if ($timeSlots instanceof TimeSlot)
        {
            $startTime = $timeSlots->start_time;
            $endTime = $timeSlots->end_time;
        }
        else if ($timeSlots instanceof Collection)
        {
            $startTime = $timeSlots->first()->start_time;
            $endTime = $timeSlots->last()->end_time;
        }
        else
        {
            throw new InvalidParameterException([$timeSlots], 'invalid parameter type.  Must be TimeSlot model or collection of TimeSlots');
        }

        $hasAnOverlappingBooking = 
            !!DB::table('bookings')
                ->join('clients', 'clients.id', '=', 'bookings.client_id')
                ->where('bookings.client_id', $this->id)
                ->whereRaw('bookings.started_at BETWEEN ? AND ?', [$startTime, $endTime])
                ->orWhereRaw('bookings.ended_at BETWEEN ? AND ?', [$startTime, $endTime])
                ->orWhereRaw('? BETWEEN bookings.started_at AND bookings.ended_at', [$startTime])
                ->count();
        
        return !$hasAnOverlappingBooking;
    }

    public function wasSentBookingConfirmation(): string
    {
        return 'email.client-booking-confirmation';        
    }
}
