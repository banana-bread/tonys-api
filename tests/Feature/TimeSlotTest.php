<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TimeSlotTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    private function makeSlotsUrl(string $companyId, $serviceDefinitionIds, string $employeeId, Carbon $from = null, Carbon $to = null): string
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

    private function makeSlotsFor(Employee $employee, int $count = 1)
    {
        $slots = collect(range(1, $count))->map(fn($num) =>
            TimeSlot::factory()->create([
                'company_id'  => $employee->company_id,
                'employee_id' => $employee->id,
                'start_time'  => today()->addDay()->addHours(9)->addMinutes((15 * ($num-1))),
                'end_time'    => today()->addDay()->addHours(9)->addMinutes((15 + (15 * ($num-1)))),
            ])
        );

        if ($slots->count() === 1)
        {
            return $slots->first();
        }

        return $slots;
    }

    private function makeServicesFor(Employee $employee, int $number = 1, string $duration = 'short')
    {
        $services = collect(range(1, $number))->map(fn($num) =>
            ServiceDefinition::factory()->{$duration}()->create(['company_id' => $employee->company_id])
        );

        $employeeServiceDefinitions = $services->map(fn($service) => [
            'employee_id'           => $employee->id, 
            'service_definition_id' => $service->id
        ])->all();

        DB::table('employee_service_definition')->insert($employeeServiceDefinitions);

        if ($services->count() === 1)
        {
            return $services->first();
        }

        return $services;
    }

    /** @test */
    public function a_single_available_time_slot_can_be_retrieved()
    {
        $emp = Employee::factory()->create();
        $service = $this->makeServicesFor($emp);
        $slot = $this->makeSlotsFor($emp);
        $url = $this->makeSlotsUrl($emp->company_id, [$service->id], $emp->id);

        $response = $this->get($url);

        $this->assertEquals($slot->id, $response->json('data.time_slots.0.id'));
    }
    /** @test */
    public function a_multi_available_time_slot_can_be_retrieved()
    {
        $emp = Employee::factory()->create();
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 2);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);
        
        $response = $this->get($url);

        $this->assertEquals($slots->first()->id, $response->json('data.time_slots.0.id'));
    }

    /** @test */
    public function a_time_slot_can_be_retrieved_without_providing_employee_id()
    {
        $s = ServiceDefinition::factory()->medium()->create();
        $ts = TimeSlot::factory()->create([
            'company_id' => $s->company_id,
            'start_time' => today()->addHours(9), 
            'end_time' => today()->addHours(9)->addMinutes(30)
        ]);
        $from = today()->timestamp;
        $to = today()->addMonth()->timestamp;

        $response = $this->get("/locations/$s->company_id"
            ."/time-slots?"
            ."service-definition-ids=$s->id&"
            ."employee-id=&"
            ."date-from=$from&"
            ."date-to=$to");

        $this->assertCount(1, $response->json('data.time_slots'));
    }
}
