<?php

namespace App\Models;

use App\Exceptions\InvalidParameterException;
use App\Models\Contracts\UserModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Traits\HasUuid;
use App\Traits\ReceivesEmails;

class Client extends BaseModel implements UserModel
{
    use HasUuid, ReceivesEmails;

    protected $appends = [
        'name',
        'phone',
        'email',
        'subscribes_to_emails',
    ];

    protected $visible = [
        'id',
        'name',
        'phone',
        'email',
        'subscribes_to_emails',

        'companies',
    ];

    // RELATIONS

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

    // CUSTOM ATTRIBUTES

    public function getNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->user->phone;
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }

    public function getSubscribesToEmailsAttribute(): bool
    {

        return $this->user->subscribed_to_emails;
    }

    // ACTIONS

    public function createBooking(TimeSlot $startingSlot, $serviceDefinitions): Booking
    {     
        $duration = $serviceDefinitions->sum('duration');
        $slotsRequired = $startingSlot->company->slotsRequiredFor($duration);
        
        $allSlots = $slotsRequired > 1
            ? $startingSlot->getNextSlots($slotsRequired)->prepend($startingSlot)
            : $startingSlot;

        if (! $this->isAvailableDuring($allSlots) || TimeSlot::isReserved($allSlots))
        {
            throw new BookingException([], 'The requested booking is not available for this client.');
        }

        TimeSlot::lock($allSlots);

        $booking = Booking::create([
            'client_id' => $this->id,
            'employee_id' => $allSlots->first()->employee_id,
            'started_at' => $allSlots->first()->start_time,
            'ended_at' => $allSlots->first()->start_time->copy()->addSeconds($duration)
        ]);

        $services = $serviceDefinitions->map(function ($definition) use ($booking) {
            $service = new Service();
            $service->service_definition_id = $definition->id;
            $service->booking_id = $booking->id;

            return $service;
        });

        $booking->services()->saveMany($services);

        return $booking;
    }

    // HELPERS

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
}
