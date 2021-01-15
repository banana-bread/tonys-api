<?php

namespace App\Services\Booking;

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
    // TODO: needs to be tested, likely broken down to be unit tested further
    public function create(array $attributes): ?Booking
    {
        return DB::transaction(function () use ($attributes) {
            $client = Client::findOrFail(Arr::get($attributes, 'client_id'));
            $startingTimeSlot = TimeSlot::findOrFail(Arr::get($attributes, 'time_slot_id'));
            $serviceDefinitions = ServiceDefinition::findOrFail(Arr::get($attributes, 'service_definition_ids'));

            $timeSlots = new Collection();
            $timeSlots->push($startingTimeSlot);

            $verifier = new BookingVerifier($startingTimeSlot);
            $verifier->verifyStillAvailable();
            $verifier->verifyNoOverlap($client);

            $singleSlotDuration = 1800;  // this really needs to be a company setting
            $bookingDuration = $serviceDefinitions->sum('duration');

            if ($bookingDuration > $singleSlotDuration)
            {
                $numberOfSlotsRequired = ceil($bookingDuration / $singleSlotDuration);
                $nextSlots = $startingTimeSlot->getNextSlots($numberOfSlotsRequired);
                $verifier->verifyNextSlotsAreAvailable($nextSlots);
                $timeSlots->push($nextSlots);
            }
            // TODO: lets test that this works
            TimeSlot::whereIn('id', Arr::pluck($timeSlots, 'id'))
                ->lockForUpdate()
                ->update(['reserved' => true]);

            $booking = Booking::create([
                'client_id' => $client->id,
                'employee_id' => $timeSlots->first()->employee_id,
                'started_at' => $timeSlots->first()->start_time,
                'ended_at' => $timeSlots->first()->start_time->copy()->addSeconds($bookingDuration)
            ]);

            $services = $serviceDefinitions->map(function ($definition) use ($booking) {
                $service = new Service();
                $service->service_definition_id = $definition->id;
                $service->booking_id = $booking->id;
                
                return $service;
            });

            $booking->services()->saveMany($services);

            return $booking;
        });
    }

    public function cancel(string $id): Booking
    {
        return DB::transaction(function () use ($id) {
            $booking = Booking::findOrFail($id);
            $booking->cancelled_at = Carbon::now();
           // $booking->cancelled_by = auth()->user()->id // TODO: implement this part
            $booking->save();

            return $booking;
        });
    }
}