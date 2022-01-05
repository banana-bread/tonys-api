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
    public function a_single_available_slot_can_be_retrieved()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slots = $this->makeSlotsFor($emp);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertEquals($slots->first()->id, $response->json('data.time_slots.0.id'));
    }

    /** @test */
    public function a_single_slot_can_be_retrieved_without_providing_employee_id()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slots = $this->makeSlotsFor($emp);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), '');

        $response = $this->get($url);

        $this->assertCount(1, $response->json('data.time_slots'));
    }    

    /** @test */
    public function specifying_employee_id_will_return_available_slots_for_only_that_particular_employee()
    {
        $emp1 = Employee::factory()->no_days_off()->create();
        $emp2 = Employee::factory()->create(['company_id' => $emp1->company_id]);
        $services = $this->makeServicesFor([$emp1, $emp2]);
        $slotsE1 = $this->makeSlotsFor($emp1);
        $slotsE2 = $this->makeSlotsFor($emp2, 1, 10);
        $url = $this->makeSlotsUrl($emp1->company_id, $services->pluck('id'), $emp1->id);

        $response = $this->get($url);
        $timeSlotData = collect($response->json('data.time_slots'));

        $this->assertFalse($timeSlotData->contains(
            fn($slot) => $slot['employee_id'] == $emp2->id
        ));
    }

    /** @test */
    public function number_of_available_time_slots_is_0_when_all_slots_are_reserved()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slots = $this->makeSlotsFor($emp);
        $slots[0]->update(['reserved' => true]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(0, $response->json('data.time_slots'));
    }

    /** @test */
    public function slots_after_the_provided_date_to_will_not_be_retrieved()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slots = $this->makeSlotsFor($emp, 2);
        $slots[1]->update(['start_time' => $slots[0]->start_time->addMonths(12), 'end_time' => $slots[0]->end_time->addMonths(12)]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);
        
        $this->assertCount(1, $response->json('data.time_slots'));
    }

    /** @test */
    public function only_one_slot_can_be_retrieved_per_start_time_when_employee_is_not_specified()
    {
        $emp1 = Employee::factory()->no_days_off()->create();
        $emp2 = Employee::factory()->create(['company_id' => $emp1->company_id]);
        $services = $this->makeServicesFor([$emp1, $emp2]);
        $slotsE1 = $this->makeSlotsFor($emp1);
        $slotsE2 = $this->makeSlotsFor($emp2);
        $url = $this->makeSlotsUrl($emp1->company_id, $services->pluck('id'), '');

        $response = $this->get($url);

        $this->assertCount(1, $response->json('data.time_slots'));
    }

    /** @test */
    public function a_slot_query_is_scoped_to_a_single_company()
    {
        $emp1 = Employee::factory()->no_days_off()->create();
        $emp2 = Employee::factory()->no_days_off()->create();
        $servicesE1 = $this->makeServicesFor($emp1);
        $servicesE2 = $this->makeServicesFor($emp2);
        $slotsE1 = $this->makeSlotsFor($emp1);
        $slotsE2 = $this->makeSlotsFor($emp2, 1, 10);
        $url = $this->makeSlotsUrl($emp1->company_id, $servicesE1->pluck('id'), '');

        $response = $this->get($url);
        $availableTimeSlots = collect($response->json('data.time_slots'));

        $this->assertCount(1, $availableTimeSlots);
        $this->assertEquals($emp1->company_id, $availableTimeSlots->first()['company_id']);
    }

    /** @test */
    public function a_slot_will_not_be_retreived_if_its_start_time_has_passed()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slots = $this->makeSlotsFor($emp);
        $slots[0]->update(['start_time' => $slots[0]->start_time->subDays(2), 'end_time' => $slots[0]->end_time->subDays(2)]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);
        
        $this->assertCount(0, $response->json('data.time_slots'));
    }

    /** @test */
    public function a_slot_will_not_be_retreived_if_now_plus_booking_grace_period_is_greater_than_its_start_time()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slots = $this->makeSlotsFor($emp);
        $slots[0]->update(['start_time' => now(), 'end_time' => now()->addMinutes(14)]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(0, $response->json('data.time_slots'));
    }
}
