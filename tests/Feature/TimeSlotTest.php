<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimeSlotTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_single_available_time_slot_can_be_retrieved()
    {
        
        $e = Employee::factory()->create();
        $s1 = ServiceDefinition::factory()->medium()->create(['company_id' => $e->company_id]);

        $ts = TimeSlot::factory()->create([
            'company_id' => $e->company_id,
            'employee_id' => $e->id, 
            'start_time' => today()->addHours(9), 
            'end_time' => today()->addHours(9)->addMinutes(30)
        ]);

        $from = today()->timestamp;
        $to = today()->addMonth()->timestamp;
        
        $response = $this->get("/locations/$e->company_id"
            ."/time-slots?"
            ."service-definition-ids=$s1->id&"
            ."employee-id=$e->id&"
            ."date-from=$from&"
            ."date-to=$to");

            $this->assertEquals($ts->id, $response->json('data.time_slots.0.id'));
    }
    /** @test */
    public function a_multi_available_time_slot_can_be_retrieved()
    {
        
        $e = Employee::factory()->create();
        $s1 = ServiceDefinition::factory()->medium()->create(['company_id' => $e->company_id]);
        $s2 = ServiceDefinition::factory()->short()->create(['company_id' => $e->company_id]);

        $ts1 = TimeSlot::factory()->create([
            'company_id' => $e->company_id,
            'employee_id' => $e->id, 
            'start_time' => today()->addHours(9), 
            'end_time' => today()->addHours(9)->addMinutes(30)
        ]);
        $ts2 = TimeSlot::factory()->create([
            'company_id' => $e->company_id,
            'employee_id' => $e->id, 
            'start_time' => today()->addHours(9)->addMinutes(30), 
            'end_time' => today()->addHours(10)
        ]);

        $from = today()->timestamp;
        $to = today()->addMonth()->timestamp;
        
        $response = $this->get("/locations/$e->company_id"
            ."/time-slots?"
            ."service-definition-ids=$s1->id,$s2->id&"
            ."employee-id=$e->id&"
            ."date-from=$from&"
            ."date-to=$to");

        $this->assertEquals($ts1->id, $response->json('data.time_slots.0.id'));
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
