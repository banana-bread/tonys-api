<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Client;
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
     
        \Log::info($request);
        $this->validate($request, [
            'client.id' => 'string|required|max:36',
            'services.id' => 'string|required|max:36',
            'services.duration' => 'int|required|max:'
            ]);
        $attributes = $request->all();

        $booking = Booking::find($id);

        $client = Client::find(Arr::get($attributes, 'client_id'));


        if ($booking->overridden || $booking->client_id)
        {
            throw new BookingException([], 'This booking is no longer available');
        }

        $bookingStart = $booking->started_at;
        $bookingEnd = $booking->ended_at;

        $hasOverlappingBooking = (bool)
            DB::table('bookings')
                ->join('clients', 'clients.id', '=', 'bookings.client_id')
                ->where('bookings.client_id', $client->id)
                ->whereRaw('bookings.started_at BETWEEN ? AND ?', [$bookingStart, $bookingEnd])
                ->orWhereRaw('bookings.ended_at BETWEEN ? AND ?', [$bookingStart, $bookingEnd])
                ->orWhereRaw('? BETWEEN bookings.started_at AND bookings.ended_at', [$bookingStart])
                ->count();
        
        if ($hasOverlappingBooking)
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
