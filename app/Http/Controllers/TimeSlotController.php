<?php

namespace App\Http\Controllers;

use App\Services\TimeSlot\TimeSlotService;

use Illuminate\Http\Request;

class TimeSlotController extends ApiController
{
    public function index(Request $request)
    {
        $service = new TimeSlotService();
        $availableSlots = $service->getAvailableSlots($request->all());
        
        return $this->success($availableSlots);
    }
}
