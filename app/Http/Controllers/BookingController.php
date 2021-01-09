<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Client;
use App\Models\ServiceDefinition;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookingController extends ApiController
{
    public function index()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: move this into a validation class
        $this->validate($request, [
            'client_id'   => 'string|nullable|max:36',
            'employee_id' => 'string|required|max:36',
            'overridden'  => 'nullable|boolean',
            'started_at'  => 'date|required|before:ended_at',
            'ended_at'    => 'date|required|after:started_at',
        ]);

        $attributes = $request->all();
        $booking = Booking::create($attributes);

        return $this->success($booking, 'Booking created', 201);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        // TODO: move this into a validation class
        $this->validate($request, [
            'client_id'   => 'string|nullable|max:36',
            'employee_id' => 'string|nullable|max:36',
            'overridden'  => 'boolean|nullable',
            'started_at'  => 'date|nullable|before:ended_at',
            'ended_at'    => 'date|nullable|after:started_at',
        ]);
        $attributes = $request->all();
        $booking = Booking::findOrFail($id);

        $booking->fill($attributes);
        $booking->save();

        return $this->success($booking, 'Booking updated');
    }

    public function reserve(Request $request, $id)
    {
        $this->validate($request, [
            'client.id' => 'string|required|max:36',

            // FIXME: this validation isn't working
            // 'service_definitions.*.id' => 'string|required',
        ]);

        $attributes = $request->all();
        $startingBookingSlot = Booking::find($id);

        $client = Client::find(Arr::get($attributes, 'client.id'));

        // check 1
        if (! $startingBookingSlot->available)
        {
            throw new BookingException([], 'This booking is no longer available');
        }

        $allBookingSlotIds = Arr::pluck($attributes['bookings'], 'id');
        $allBookingSlots = Booking::orderBy('started_at')->find($allBookingSlotIds);
        
        $serviceDefinitionIds = Arr::pluck($attributes['service_definitions'], 'id');
        $serviceDefinitions = ServiceDefinition::find($serviceDefinitionIds);

        $totalDurationOfServices = $serviceDefinitions->sum('duration');
        $lengthOfOneBooking = 1800; // should probably store this in company settings table
        $requiresManyBookingSlots = $totalDurationOfServices > $lengthOfOneBooking;

        // check 2
        if ($requiresManyBookingSlots &&
            !$startingBookingSlot->employee->hasFullAvailabilityIn($allBookingSlots))
        {

            throw new BookingException([], 'The total duration of services exceeds one booking slot, and some of the following slots are unavailable.');
        }

        $bookingStart = $allBookingSlots->first()->started_at;
        $bookingEnd = $allBookingSlots->last()->ended_at;

        $hasAnOverlappingBooking = 
            !!DB::table('bookings')
                ->join('clients', 'clients.id', '=', 'bookings.client_id')
                ->where('bookings.client_id', $client->id)
                ->whereRaw('bookings.started_at BETWEEN ? AND ?', [$bookingStart, $bookingEnd])
                ->orWhereRaw('bookings.ended_at BETWEEN ? AND ?', [$bookingStart, $bookingEnd])
                ->orWhereRaw('? BETWEEN bookings.started_at AND bookings.ended_at', [$bookingStart])
                ->count();

        // check 3
        if ($hasAnOverlappingBooking)
        {
            throw new BookingException([], 'The booking you are trying to reserve overlaps with an existing booking for this client.');
        }

        $booking->client_id = $client->id;
        $booking->save();

        return $this->success($booking, 'Booking reserved');

    }

    public function destroy($id)
    {
        //
    }
}
