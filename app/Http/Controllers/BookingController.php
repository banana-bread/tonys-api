<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Mail\BookingCancelled;
use App\Models\Booking;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function destroy(string $companyId, string $id): JsonResponse
    {
        DB::transaction(function () use ($companyId, $id) 
        {            
            $booking = Booking::forCompany($companyId)->findOrFail($id);
            $booking->cancel();
    
            if (!! $booking->client)
            {
                $booking->client->send(new BookingCancelled($booking));
    
                if ($booking->wasCancelledBy($booking->client))
                {
                    $booking->employee->send(new BookingCancelled($booking));
                }
            }
        });

        return $this->deleted('Booking cancelled.');
    }
}
