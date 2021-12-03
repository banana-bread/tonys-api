<?php

namespace App\Http\Controllers;

use App\Services\TimeSlot\TimeSlotService;
use App\Http\Requests\TimeSlotRequest;

class TimeSlotController extends ApiController
{
    public function index(TimeSlotRequest $request, string $companyId)
    {
        $slots = (new TimeSlotService())->getAvailableSlots($companyId);

        return $this->ok(['time_slots' => $slots], 'Available time slots retrieved.');
    }
}
