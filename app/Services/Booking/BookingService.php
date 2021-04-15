<?php

namespace App\Services\Booking;

use App\Mail\BookingCreated;
use App\Models\Booking;
use App\Models\Client;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use Illuminate\Support\Arr;
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

            $booking = $client->createBooking($startingTimeSlot, $serviceDefinitions);
            
            if ($client->subscribes_to_emails)
            {
                $client->send(new BookingCreated($booking));
            }

            return $booking;
        });
    }

    public function get(string $id): ?Booking
    {
        return Booking::findOrFail($id);
    }

}