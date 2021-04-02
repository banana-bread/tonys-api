<?php

namespace App\Services\TimeSlot;

use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\ServiceDefinition;
use App\Services\TimeSlot\TimeSlotPdo;
use Carbon\Carbon;
use App\Http\Requests\TimeSlotRequest;

class TimeSlotService
{
    public function getAvailableSlots(TimeSlotRequest $request): Collection
    {   
        $serviceDefinitionIds = Str::of( request('service-definition-ids') )->explode(',');
        $employeeId = request('employee-id');
        
        $dateFrom = Carbon::createFromTimestamp( request('date-from') );
        $dateTo = Carbon::createFromTimestamp( request('date-to') );

        $singleSlotDuration = 1800; // 30 minutes... this should be a company setting
        $serviceDefinitions = ServiceDefinition::find($serviceDefinitionIds);
        $companyId = $serviceDefinitions->first()->company->id;

        $summedServiceDuration = $serviceDefinitions->sum('duration');
        $slotsRequired = ceil($summedServiceDuration / $singleSlotDuration);

        $tsPdo = new TimeSlotPdo($dateFrom, $dateTo, $companyId, $employeeId);

        $slots = $slotsRequired > 1
            ? $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired)
            : $tsPdo->fetchAvailableSlots();
            
        return $slots;
    }
}
