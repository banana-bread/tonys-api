<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;

class ClientBookingController extends ApiController
{
    public function index(string $clientId)
    {
        $dateFrom = Carbon::createFromTimestamp(request('date-from'));
        $dateTo = Carbon::createFromTimestamp(request('date-to'));

        $bookings = Booking::with('employee.company')
            ->without(['client'])
            ->where('client_id', $clientId)
            ->whereBetween('ended_at', [$dateFrom, $dateTo])
            ->orderBy('started_at', 'asc')
            ->get();

        return $this->ok(['bookings' => $bookings], 'Bookings retreived');
    }
}
