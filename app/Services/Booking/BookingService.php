<?php

namespace App\Services\Booking;

use App\Mail\BookingCreated;
use App\Models\Booking;
use App\Models\Client;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function create(): ?Booking
    {
        return DB::transaction(function () {
            // getting query data
            $client = Client::findOrFail(request('client.id'));
            $startingTimeSlot = TimeSlot::findOrFail(request('time_slot_id'));
            $serviceDefinitionIds = collect(request('services'))->pluck('id');
            $serviceDefinitions = ServiceDefinition::findOrFail($serviceDefinitionIds);
            $booking = $client->createBooking($startingTimeSlot, $serviceDefinitions);
            $note = request('note');

            if ($note && $note['body'])
            {
              $booking->note()->create(['body' => $note['body']]);
            }
            
            // TODO move this check into the trait?
            if ($client->subscribes_to_emails)
            {
                $client->send(new BookingCreated($booking));
            }

            return $booking;
        });
    }
}