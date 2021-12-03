<?php

namespace App\Services\Booking;

use App\Models\Client;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Exceptions\BookingException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingVerifier
{
    protected TimeSlot $timeSlot;

    public function __construct(TimeSlot $timeSlot)
    {
        $this->timeSlot = $timeSlot;
    }

    public function verifyStillAvailable(): void
    {
        if ($this->timeSlot->reserved)
        {
            throw new BookingException([], 'This time slot is no longer available');
        }
    }


    
    public function verifyNoOverlap(Client $client): void
    {
        /*
            if (!! $ids->isArray())
            {
                $startTime = $ids->first()->start_time;
                $endTime = $ids->last()->end_time;
            }
            else if (isString)
            {
                $startTime = $ids->start_time;
                $endTime = $ids->end_time;
            }
        */
        $hasAnOverlappingBooking = 
            !!DB::table('bookings')
                ->join('clients', 'clients.id', '=', 'bookings.client_id')
                ->where('bookings.client_id', $client->id)
                ->whereRaw('bookings.started_at BETWEEN ? AND ?', [$this->timeSlot->start_time, $this->timeSlot->end_time])
                ->orWhereRaw('bookings.ended_at BETWEEN ? AND ?', [$this->timeSlot->start_time, $this->timeSlot->end_time])
                ->orWhereRaw('? BETWEEN bookings.started_at AND bookings.ended_at', [$this->timeSlot->start_time])
                ->count();

        if ($hasAnOverlappingBooking)
        {
            throw new BookingException([], 'The time slot you are trying to reserve overlaps with an existing booking for this client.');
        }
    }

    public function verifyNextSlotsAreAvailable(Collection $nextSlots): void
    {
        $containsReservedSlot = $nextSlots->contains(function ($slot) {
            return $slot->reserved;
        });

        if ($containsReservedSlot ||
            $nextSlots->isEmpty())
        {
            throw new BookingException([], 'The total duration of services exceeds one booking slot, and some of the following slots are unavailable.');
        }
    }
}
