<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Mail\BookingCancelled;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;

class BookingController extends ApiController
{
    public function index(string $companyId)
    {
        // TODO: this may end up being moved to EmployeeBookingController
        $bookings = Booking::forCompany($companyId)
            // ->select('bookings.*', '''clients.name', )
            ->whereDate('started_at', Carbon::createFromTimestamp(request('date_for')) )
            ->whereNull('cancelled_at')
            ->orderBy('started_at')
            ->get()
            ->groupBy('employee_id');

        return $this->ok(['employee_bookings' => $bookings], 'Bookings retreived');
    }

    public function store(CreateBookingRequest $request): JsonResponse
    {
        $booking = (new BookingService())->create();

        return $this->created(['booking' => $booking], 'Booking created.');
    }

    public function show(string $companyId, string $id): JsonResponse
    {
        return $this->ok(
            ['booking' => Booking::forCompany($companyId)->with('services')->findOrFail($id)], 
            'Booking retreived.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $booking = Booking::findOrFail($id);

        if (!$this->_authedUserIsBookingsClient($booking) && 
            !$this->_authedUserBelongsToBookingsCompany($booking))
        {
            throw new AuthorizationException('User not authorized.');
        }

        $booking->cancel();

        return $this->deleted('Booking cancelled.');
    }

    private function _authedUserIsBookingsClient(Booking $booking): bool
    {
        return auth()->user()->isClient() && 
               auth()->user()->client->id === $booking->client_id;
    }

    private function _authedUserBelongsToBookingsCompany(Booking $booking): bool
    {
        return auth()->user()->isEmployee() && 
               auth()->user()->employee->company_id === $booking->employee->company_id;
    }
}
