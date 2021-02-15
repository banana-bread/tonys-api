<?php

namespace App\Http\Controllers;

use App\Services\TimeSlot\TimeSlotService;
use App\Http\Requests\TimeSlotRequest;

class TimeSlotController extends ApiController
{
    public function index(TimeSlotRequest $request)
    {
        $service = new TimeSlotService();
        $slots = $service->getAvailableSlots($request->all());

        return $this->success(['time_slots' => $slots], 'Available time slots retrieved.');
    }
}
