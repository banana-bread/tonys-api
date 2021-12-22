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

class TimeSlotTest extends TestCase
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
    public function a_time_slot_can_be_retrieved_without_providing_employee_id()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp);
        $slots = $this->makeSlotsFor($emp);
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
    public function when_a_client_requests_many_with_summed_durations_requiring_4_slots_then_only_time_slots_with_3_available_slots_after_are_shown()
    {
        $emp = Employee::factory()->no_days_off()->create();
        $services = $this->makeServicesFor($emp, 4);
        $slots = $this->makeSlotsFor($emp, 8);
        $slots[1]->update(['reserved' => true]);
        $url = $this->makeSlotsUrl($emp->company_id, $services->pluck('id'), $emp->id);

        $response = $this->get($url);

        $this->assertCount(2, $response->json('data.time_slots'));
    }

      /** @test */
      public function specifying_employee_id_will_return_available_slots_for_only_that_particular_employee()
      {
          $emp1 = Employee::factory()->no_days_off()->create();
          $emp2 = Employee::factory()->create(['company_id' => $emp1->company_id]);
          $services = $this->makeServicesFor($emp1);
          $slots = $this->makeSlotsFor($emp1, 4);
          $url = $this->makeSlotsUrl($emp1->company_id, $services->pluck('id'), $emp->id);

          $response = $this->get($url);

          $this->assertFalse($response->json('data.time_slots')->contains(
              fn($slot) => $slot['employee_id'] == $emp2->id
          ));
      }
    
}
