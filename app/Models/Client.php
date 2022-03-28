<?php

namespace App\Models;

use App\Exceptions\BookingException;
use App\Exceptions\InvalidParameterException;
use App\Models\Contracts\UserModel;
use Illuminate\Support\Collection;
use App\Traits\HasUuid;
use App\Traits\ReceivesEmails;
use Carbon\Carbon;

class Client extends BaseModel implements UserModel
{
    use HasUuid, ReceivesEmails;

    protected $appends = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'subscribes_to_emails',
    ];

    protected $visible = [
        'id',

        'companies',
        'bookings',

        'first_name',
        'last_name',
        'phone',
        'email',
        'subscribes_to_emails',
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
        return $this->belongsToMany(Company::class, 'companies_clients')->withTimestamps();
    }

    // CUSTOM ATTRIBUTES

    public function getFirstNameAttribute(): string
    {
        return $this->user->first_name;
    }

    public function getLastNameAttribute(): string
    {
        return $this->user->last_name;
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
        if (! Employee::find($startingSlot->employee_id)->isActive())
        {
            throw new BookingException([], 'Cannot create booking with inactive employee.');
        }

        $duration = $serviceDefinitions->sum('duration');
        $slotsRequired = $startingSlot->company->slotsRequiredFor($duration);


        $allSlots = $slotsRequired > 1
            ? $startingSlot->getNextSlots($slotsRequired)->prepend($startingSlot)
            : collect([$startingSlot]);

        // if (! $this->isAvailableDuring($allSlots))
        // {
        //     throw new BookingException([], 'You already have a booking at this time.');
        // }

        if (! TimeSlot::isAvailable($allSlots))
        {
            throw new BookingException([], 'Time slot is no longer available.');
        }

        $booking = Booking::create([
            'client_id' => $this->id,
            'employee_id' => $allSlots->first()->employee_id,
            'started_at' => $allSlots->first()->start_time,
            'ended_at' => Carbon::parse($allSlots->first()->start_time)->addSeconds($duration)
        ]);

        TimeSlot::lockAndReserve($allSlots, $booking);

        // TODO: Service::fromDefinitions(Collection $definitions) ?
        $services = $serviceDefinitions->map(function ($definition) use ($booking) {
            $service = new Service();
            $service->service_definition_id = $definition->id;
            $service->booking_id = $booking->id;
            $service->name = $definition->name;
            $service->description = $definition->description;
            $service->price = $definition->price;
            $service->duration = $definition->duration;

            return $service;
        });

        $booking->services()->saveMany($services);
        $this->attachOrUpdateCompany($booking->employee->company_id);

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

        // $startTime->addSecond();
   
        $hasAnOverlappingBooking = 
            $this->bookings()
                ->where('client_id', $this->id)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereRaw('bookings.started_at BETWEEN ? AND ?', [$startTime, $endTime])
                        ->orWhereRaw('bookings.ended_at BETWEEN ? AND ?', [$startTime, $endTime])
                        ->orWhereRaw('? BETWEEN bookings.started_at AND bookings.ended_at', [$startTime]);
                })
                ->exists();

        return !$hasAnOverlappingBooking;
    }

    public function attachOrUpdateCompany(string $companyId)
    {
        $this->companies()->where('companies.id', $companyId)->exists()
            ?  $this->companies()->updateExistingPivot($companyId, ['updated_at' => now()])
            :  $this->companies()->attach($companyId);
    }
}
