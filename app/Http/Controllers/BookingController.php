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

        return $this->success(['booking' => $booking], 'Booking created.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $service = new BookingService();
        $booking = $service->get($id);

        return $this->success(['booking' => $booking], 'Booking found.');
    }

    public function destroy(string $id): JsonResponse
    {
        $service = new BookingService();
        $service->cancel($id);

        return $this->success([], 'Booking cancelled.', 204);
    }
}
