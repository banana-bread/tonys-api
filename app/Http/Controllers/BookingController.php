<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Mail\BookingCancelled;
use App\Models\Booking;
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
        $booking = (new BookingService())->create();

        return $this->created(['booking' => $booking], 'Booking created.');
    }

    public function show(string $companyId, string $id): JsonResponse
    {
        return $this->ok(
            ['booking' => Booking::forCompany($companyId)->findOrFail($id)], 'Booking retreived.'
        );
    }

    public function destroy(string $companyId, string $id): JsonResponse
    {
        $booking = Booking::forCompany($companyId)->findOrFail($id);
        $booking->cancel();

        $booking->client->send(new BookingCancelled($booking));

        if ($booking->wasCancelledBy($booking->client))
        {
            $booking->employee->send(new BookingCancelled($booking));
        }

        return $this->deleted('Booking cancelled.');
    }
}
