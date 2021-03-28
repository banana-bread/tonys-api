<?php

namespace App\Services\Booking;

use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function create(array $attributes): ?Booking
    {
        return DB::transaction(function () use ($attributes) {
            // getting query data
            $client = Client::findOrFail(Arr::get($attributes, 'client_id'));
            $startingTimeSlot = TimeSlot::findOrFail(Arr::get($attributes, 'time_slot_id'));
            $serviceDefinitions = ServiceDefinition::findOrFail(Arr::get($attributes, 'service_definition_ids'));
            $bookingDuration = $serviceDefinitions->sum('duration');

            $singleSlotDuration = 1800;  // this really needs to be a company setting
            $numberOfSlotsRequired = ceil($bookingDuration / $singleSlotDuration);

            $allTimeSlots = $numberOfSlotsRequired > 1
                ? $startingTimeSlot->getNextSlots($numberOfSlotsRequired)->prepend($startingTimeSlot)
                : $startingTimeSlot;

            $this->_verifySlotsAreAvailable($allTimeSlots, $client);
            $booking = $this->_createBooking($allTimeSlots, $client, $bookingDuration, $serviceDefinitions);
            
            if ($client->subscribes_to_emails)
            {
                $booking->notify($client);
            }

            return $booking;
        });
    }

    protected function _verifySlotsAreAvailable($allTimeSlots, Client $client)
    {
        $isReserved = $allTimeSlots instanceof TimeSlot
            ? $allTimeSlots->reserved
            : $allTimeSlots->contains(function ($slot) { return $slot->reserved; });

        if ($isReserved || !$client->isAvailableDuring($allTimeSlots))
        {
            throw new BookingException([], 'The requested booking is not available for this client.');
        }
    }

    protected function _createBooking($allTimeSlots, Client $client, int $bookingDuration, $serviceDefinitions): Booking
    {
        // TODO: lets test that this works.  It's for race conditions
        // marking timeslots(s) as reserved
        TimeSlot::whereIn('id', Arr::pluck($allTimeSlots, 'id'))
            ->lockForUpdate()
            ->update(['reserved' => true]);

        // creating booking
        $booking = Booking::create([
            'client_id' => $client->id,
            'employee_id' => $allTimeSlots->first()->employee_id,
            'started_at' => $allTimeSlots->first()->start_time,
            'ended_at' => $allTimeSlots->first()->start_time->copy()->addSeconds($bookingDuration)
        ]);

        // creating services for booking
        $services = $serviceDefinitions->map(function ($definition) use ($booking) {
            $service = new Service();
            $service->service_definition_id = $definition->id;
            $service->booking_id = $booking->id;
            
            return $service;
        });

        $booking->services()->saveMany($services);

        return $booking;
    }

    public function cancel(string $id): Booking
    {
        return DB::transaction(function () use ($id) {
            $booking = Booking::findOrFail($id);
            $booking->cancel(auth()->user());

            return $booking;
        });
    }
}