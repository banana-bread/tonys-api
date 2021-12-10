<?php

namespace App\Services\TimeSlot;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\ServiceDefinition;
use App\Services\TimeSlot\TimeSlotPdo;
use Carbon\Carbon;
use App\Http\Requests\TimeSlotRequest;

class TimeSlotService
{
    public function getAvailableSlots(string $companyId)
    {   
        $employeeId = request('employee-id');
        $serviceDefinitions = ServiceDefinition::find(
            Str::of(request('service-definition-ids'))->explode(',')
        );
        
        $dateFrom = Carbon::createFromTimestamp( request('date-from') );
        $dateTo = Carbon::createFromTimestamp( request('date-to') );

        $tsPdo = new TimeSlotPdo($dateFrom, $dateTo, $companyId, $serviceDefinitions, $employeeId);
        $slots = $tsPdo->execute();

        return $slots;
    }
}
