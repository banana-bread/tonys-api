<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Services\Booking\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends ApiController
{
    public function index()
    {
        // TODO: figure out pagination
    }

    public function store(CreateBookingRequest $request): JsonResponse
    {
        $service = new BookingService();
        $booking = $service->create($request->all());

        return $this->created(['booking' => $booking], 'Booking created.');
    }

    public function show(string $id): JsonResponse
    {
        $service = new BookingService();
        $booking = $service->get($id);

        return $this->ok(['booking' => $booking], 'Booking found.');
    }

    public function destroy(string $id): JsonResponse
    {
        $service = new BookingService();
        $service->cancel($id);

        return $this->deleted('Booking cancelled.');
    }
}
