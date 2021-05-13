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
    public function getAvailableSlots(string $companyId): Collection
    {   
        $employeeId = request('employee-id');
        $serviceDefinitions = ServiceDefinition::find(
            Str::of(request('service-definition-ids'))->explode(',')
        );
        
        $dateFrom = Carbon::createFromTimestamp( request('date-from') );
        $dateTo = Carbon::createFromTimestamp( request('date-to') );
        $singleSlotDuration = $serviceDefinitions->first()->company->time_slot_duration;

        $summedServiceDuration = $serviceDefinitions->sum('duration');
        $slotsRequired = ceil($summedServiceDuration / $singleSlotDuration);

        $tsPdo = new TimeSlotPdo($dateFrom, $dateTo, $companyId, $employeeId);

        $slots = $slotsRequired > 1
            ? $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired)
            : $tsPdo->fetchAvailableSlots();
            
        return $slots;
    }
}
