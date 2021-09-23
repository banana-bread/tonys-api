<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeBookingRequest;
use App\Mail\BookingCancelled;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class EmployeeBookingController extends ApiController
{

    public function store(CreateEmployeeBookingRequest $request, string $company_id, string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $serviceDefinitions = ServiceDefinition::whereIn(
            'id', collect(request('services'))->pluck('id')
        )->get();
        $startingSlot = $employee->time_slots()->where('start_time', request('event.start'))->first();
        $booking = $employee->createBooking($startingSlot, $serviceDefinitions);

        return $this->ok(['booking' => $booking], 'Booking created.');
    }
}
