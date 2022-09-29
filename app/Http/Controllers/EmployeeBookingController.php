<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingException;
use App\Http\Requests\CreateEmployeeBookingRequest;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceDefinition;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeBookingController extends ApiController
{

    public function store(CreateEmployeeBookingRequest $request, string $company_id, string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        if (auth()->user()->id !== $employee->user_id && ! auth()->user()->isAdmin())
        {
            throw new BookingException([], 'User not authorized to perform this action.');
        }

        $bookingType = request('type');
        $startingSlot = $employee->time_slots()->where('start_time', request('started_at'))->first();
        $startedAt = Carbon::parse(request('started_at'));
        $endedAt = Carbon::parse(request('ended_at'));
        $manualClientName = request('manual_client_name');
        $serviceDefinitions = ServiceDefinition::whereIn('id', collect(request('services'))->pluck('id'))->get();
        $noteBody = request('note.body');
        $duration = $bookingType === Booking::TYPE_APPOINTMENT
          ? $serviceDefinitions->sum('duration')
          : $startedAt->diffInSeconds($endedAt);       

        $booking = DB::transaction(function () use ($employee, $startingSlot, $duration, $noteBody, $bookingType, $manualClientName, $serviceDefinitions)
        {
            $booking = $employee->createBooking($startingSlot, $duration, $bookingType, $manualClientName);
        
            if ($booking->type === Booking::TYPE_APPOINTMENT)
            {
              $services = $serviceDefinitions->map(function ($definition) use ($booking) {
                  $service = new Service();
                  $service->service_definition_id = $definition->id;
                  $service->booking_id = $booking->id;
                  $service->name = $definition->name;
                  $service->description = $definition->description;
                  $service->price = $definition->price;
                  $service->duration = $definition->duration;
      
                  return $service;
              });
      
              $booking->services()->saveMany($services);
            }

            if (!!$noteBody)
            {
              $booking->note()->create(['body' => $noteBody]);
            }

            return $booking;
        });

        $booking->load('services');
        $booking->load('note');

        return $this->ok(['booking' => $booking], 'Booking created.');
    }
}
