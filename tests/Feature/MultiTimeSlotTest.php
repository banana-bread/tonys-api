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

class MultiTimeSlotTest extends TestCase
{
    use WithFaker, RefreshDatabase, MocksTimeSlots;

    /** @test */
    public function a_multi_available_time_slot_can_be_retrieved()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 2);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertEquals($slots->first()->id, $response->json('data.time_slots.0.id'));
    }

    /** @test */
    public function a_multi_time_slot_can_be_retrieved_without_providing_employee_id()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 2);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), '');

        $response = $this->get($url);

        $this->assertCount(1, $response->json('data.time_slots'));
    }

    /** @test */
    public function when_a_client_requests_services_with_summed_durations_requiring_2_slots_then_only_time_slots_with_an_available_slot_after_are_shown()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 1, 'medium');
        $slots = $this->makeSlotsFor($emp, 4);
        $slots[1]->update(['reserved' => true]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(1, $response->json('data.time_slots'));
    }

    /** @test */
    public function when_a_client_requests_services_with_summed_durations_requiring_3_slots_then_only_time_slots_with_2_available_slots_after_are_shown()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 1, 'long');
        $slots = $this->makeSlotsFor($emp, 5);
        $slots[1]->update(['reserved' => true]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(1, $response->json('data.time_slots'));
    }

    /** @test */
    public function when_a_client_requests_services_with_summed_durations_requiring_4_slots_then_only_time_slots_with_3_available_slots_after_are_shown()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 4);
        $slots = $this->makeSlotsFor($emp, 8);
        $slots[1]->update(['reserved' => true]);
        $slots[7]->update(['reserved' => true]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(2, $response->json('data.time_slots'));
    }

    /** @test */
    public function specifying_employee_id_will_return_available_slots_for_only_that_particular_employee()
    {
        $emp1 = Employee::factory()->no_days_off()->create();
        $emp2 = Employee::factory()->create(['company_id' => $emp1->company_id]);
        $services = $this->makeServicesFor([$emp1, $emp2]);
        $slotsE1 = $this->makeSlotsFor($emp1, 4);
        $slotsE2 = $this->makeSlotsFor($emp2, 4, 10);

        $url = $this->makeSlotsUrl($emp1->company_id, $services->pluck('id'), $emp1->id);

        $response = $this->get($url);
        $timeSlotData = collect($response->json('data.time_slots'));

        $this->assertFalse($timeSlotData->contains(
            fn($slot) => $slot['employee_id'] == $emp2->id
        ));
    }

    /** @test */
    public function a_time_slot_only_counts_towards_consecutiveness_if_they_are_on_the_same_day()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 2);
        $slots[1]->update([
            'start_time' => $slots[1]->start_time->addDay(), 
            'end_time' => $slots[1]->end_time->addDay()
        ]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(0, $response->json('data.time_slots'));
    }
    
    /** @test */
    public function a_time_slot_only_counts_towards_consecutiveness_if_they_belong_to_the_same_employee()
    {
        $emp1 = Employee::factory()->no_days_off()->create();
        $emp2 = Employee::factory()->create(['company_id' => $emp1->company_id]);
        $services = $this->makeServicesFor([$emp1, $emp2]);
        $slotsE1 = $this->makeSlotsFor($emp1, 4);
        $slotsE2 = $this->makeSlotsFor($emp2, 4, 10);
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
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 2);
        $slots[1]->update(['reserved' => true]);

        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(0, $response->json('data.time_slots'));
    }


    /** @test */
    public function time_slots_after_the_provided_date_to_will_not_be_retrieved()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 4);
        $slots[0]->update(['start_time' => $slots[0]->start_time->addMonths(12), 'end_time' => $slots[0]->end_time->addMonths(12)]);
        $slots[1]->update(['start_time' => $slots[0]->start_time->addMonths(12), 'end_time' => $slots[0]->end_time->addMonths(12)]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);
        
        $this->assertCount(1, $response->json('data.time_slots'));
    }

    /** @test */
    public function only_one_time_slot_can_be_retrieved_per_start_time_when_employee_is_not_specified()
    {
        $emp1 = Employee::factory()->no_days_off()->create();
        $emp2 = Employee::factory()->create(['company_id' => $emp1->company_id]);
        $services = $this->makeServicesFor([$emp1, $emp2], 2);
        $slotsE1 = $this->makeSlotsFor($emp1, 2);
        $slotsE2 = $this->makeSlotsFor($emp2, 2);
        $url = $this->makeSlotsUrl($emp1->company_id, $services->pluck('id'), '');

        $response = $this->get($url);

        $this->assertCount(1, $response->json('data.time_slots'));
    }

    /** @test */
    public function a_time_slot_query_is_scoped_to_a_single_company()
    {
        $emp1 = Employee::factory()->no_days_off()->create();
        $emp2 = Employee::factory()->no_days_off()->create();
        $servicesE1 = $this->makeServicesFor($emp1, 2);
        $servicesE2 = $this->makeServicesFor($emp2, 2);
        $slotsE1 = $this->makeSlotsFor($emp1, 2);
        $slotsE2 = $this->makeSlotsFor($emp2, 2, 10);
        $url = $this->makeSlotsUrl($emp1->company_id, $servicesE1->pluck('id'), '');

        $response = $this->get($url);
        $availableTimeSlots = collect($response->json('data.time_slots'));

        $this->assertCount(1, $availableTimeSlots);
        $this->assertEquals($emp1->company_id, $availableTimeSlots->first()['company_id']);
    }

    /** @test */
    public function a_time_slot_will_not_be_retreived_if_its_start_time_has_passed()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 2);
        $slots[0]->update(['start_time' => $slots[0]->start_time->subDays(2), 'end_time' => $slots[0]->end_time->subDays(2)]);
        $slots[1]->update(['start_time' => $slots[0]->start_time->subDays(2), 'end_time' => $slots[0]->end_time->subDays(2)]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);
        
        $this->assertCount(0, $response->json('data.time_slots'));
    }

    /** @test */
    public function a_time_slot_will_not_be_retreived_if_now_plus_booking_grace_period_is_greater_than_its_start_time()
    {
        Carbon::setTestNow( today()->addDays(1)->addHours(8)->addMinutes(46) );
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 2);
        $slots[0]->update(['start_time' => $slots[0]->start_time->subDay(), 'end_time' => $slots[0]->end_time->subDay()]);
        $slots[1]->update(['start_time' => $slots[0]->start_time->subDay(), 'end_time' => $slots[0]->end_time->subDay()]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(0, $response->json('data.time_slots'));
    }

    /** @test */
   public function a_time_slot_will_not_be_retreived_if_the_requested_employee_is_not_working()
   {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 2);
        $slots = $this->makeSlotsFor($emp, 2);
        $slots[0]->update(['employee_working' => false]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);
    
        $this->assertCount(0, $response->json('data.time_slots'));
   }


    /** @test */
    public function a_time_slot_will_not_be_retreived_if_the_requested_employee_has_online_bookings_disabled()
    {
         $emp = Employee::factory()->no_days_off()->create(['bookings_enabled' => false]);
         $services = $this->makeServicesFor($emp, 2);
         $slots = $this->makeSlotsFor($emp, 2);
         $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);
 
         $response = $this->get($url);
     
         $this->assertCount(0, $response->json('data.time_slots'));
    }

    /** @test */
    public function time_slots_will_not_be_shown_for_employees_when_a_service_is_selected_that_the_employee_cannot_perform()
    {
        $emp1 = Employee::factory()->no_days_off()->create();
        $emp2 = Employee::factory()->no_days_off()->create(['company_id' => $emp1->company_id]);
        $servicesE1 = $this->makeServicesFor($emp1, 2);
        $servicesE2 = $this->makeServicesFor($emp2, 2);
        $services = collect([ $servicesE1[0],  $servicesE2[0] ]);

        $slots = $this->makeSlotsFor($emp1, 2);
        $url = $this->makeSlotsUrl($emp1->company_id, $services->pluck('id'), $emp1->id);

        $response = $this->get($url);
    
        $this->assertCount(0, $response->json('data.time_slots'));
    }
}
