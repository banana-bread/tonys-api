<?php

namespace Tests;

use App\Models\Employee;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait MocksTimeSlots 
{

    public function makeSlotsUrl(string $companyId, $serviceDefinitionIds, string $employeeId, Carbon $from = null, Carbon $to = null): string
    {
        if (!$from)
        {
            $from = today()->subMonth();
            $to = $from->copy()->addMonths(2);
        }

        $serviceDefinitionIds = collect($serviceDefinitionIds)->implode(',');

        return "/locations/$companyId"
            ."/time-slots?"
            ."service-definition-ids=$serviceDefinitionIds&"
            ."employee-id=$employeeId&"
            ."date-from=$from->timestamp&"
            ."date-to=$to->timestamp";
    }

    public function makeSlotsFor(Employee $employee, int $count = 1, int $startingHour = 9)
    {
        $slots = collect(range(1, $count))->map(fn($num) =>
            TimeSlot::factory()->create([
                'company_id'  => $employee->company_id,
                'employee_id' => $employee->id,
                'start_time'  => today()->addDay()->addHours($startingHour)->addMinutes((15 * ($num-1))),
                'end_time'    => today()->addDay()->addHours($startingHour)->addMinutes((15 + (15 * ($num-1)))),
            ])
        );

        if ($slots->count() === 1)
        {
            return $slots->first();
        }

        return $slots;
    }

    public function makeServicesFor($employees, int $number = 1, string $duration = 'short')
    {
        if ($employees instanceof Employee)
        {
            $employees = collect([$employees]);
        }

        if (! $employees instanceof Collection)
        {
            $employees = collect($employees);
        }

        $services = collect(range(1, $number))->map(fn($num) =>
            ServiceDefinition::factory()->{$duration}()->create(['company_id' => $employees->first()->company_id])
        );

        $employeeServiceDefinitions = $services->flatMap(fn($service) => 
            $employees->map(fn($emp) => [
                'employee_id'           => $emp->id, 
                'service_definition_id' => $service->id
            ])
        )->all();

        DB::table('employee_service_definition')->insert($employeeServiceDefinitions);
        
        return $services;
    }
}