<?php

namespace App\Services\TimeSlot;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\ServiceDefinition;
use App\Services\TimeSlot\TimeSlotPdo;

class TimeSlotService
{
    public function getAvailableSlots(array $attributes): Collection
    {
        $serviceDefinitionIdsString = Arr::get($attributes, 'service-definition-ids');
        $serviceDefinitionIds = Str::of($serviceDefinitionIdsString)->explode(',');
        $employeeId = Arr::get($attributes, 'employee-id');
        
        $dateFrom = Arr::get($attributes, 'date-from');
        $dateTo = Arr::get($attributes, 'date-to');

        $singleSlotDuration = 1800; // 30 minutes... this should be a company setting
        $serviceDefinitions = ServiceDefinition::find($serviceDefinitionIds);
        $summedServiceDuration = $serviceDefinitions->sum('duration');
        $slotsRequired = ceil($summedServiceDuration / $singleSlotDuration);

        // TODO: rename TimeSlotPdo to something else
        $tsPdo = new TimeSlotPdo($dateFrom, $dateTo, $employeeId);

        $slots = $slotsRequired > 1
            ? $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired)
            : $tsPdo->fetchAvailableSlots();

        return $slots;
    }
}
