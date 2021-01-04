<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


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

        $clientIsReservingBooking = !$booking->client_id && Arr::get($attributes, 'client_id');

        if ($clientIsReservingBooking)
        {
            // 1. check that client does not have overlapping bookings with any other employees
            
            
        }


        // $overriddenStatusHasChanged = $booking->overridden !== Arr::get($attributes, 'overridden');
        $booking->fill($attributes);
        $booking->save();

        return $this->success($booking, 'Booking updated');
    }

    public function destroy($id)
    {
        //
    }
}
