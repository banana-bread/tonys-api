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
use Tests\MocksTimeSlots;

class SingleTimeSlotTest extends TestCase
{
    use WithFaker, RefreshDatabase, MocksTimeSlots;

    /** @test */
    public function a_single_available_time_slot_can_be_retrieved()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slot = $this->makeSlotsFor($emp);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertEquals($slot->id, $response->json('data.time_slots.0.id'));
    }

    /** @test */
    public function a_single_time_slot_can_be_retrieved_without_providing_employee_id()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slots = $this->makeSlotsFor($emp);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), '');

        $response = $this->get($url);

        $this->assertCount(1, $response->json('data.time_slots'));
    }    
}
