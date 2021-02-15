<?php

namespace Tests\Unit\Services\TimeSlot;

use App\Models\Employee;
use App\Models\TimeSlot;
use App\Services\TimeSlot\TimeSlotPdo;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimeSlotPdoTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    // This may still be useful for potenatial feature tests
    // $response = $this->get("/time-slots?"
    //     ."service-definition-ids=$s1->id,$s2->id&"
    //     ."employee-id=$e->id&"
    //     ."date-from=$from&"
    //     ."date-to=$to");

    /** @test */
    public function when_a_client_requests_many_services_with_summed_durations_requiring_2_slots_then_only_time_slots_with_an_available_slot_after_are_shown()
    {
        $employee = Employee::factory()->create();

        $available1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id]);
        $reserved = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30),'end_time' => Carbon::today()->addHours(10),'employee_id' => $employee->id]);
        $available2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id]);
        $available3 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10)->addMinutes(30),'end_time' => Carbon::today()->addHours(11),'employee_id' => $employee->id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

    /** @test */
    public function when_a_client_requests_many_services_with_summed_durations_requiring_3_slots_then_only_time_slots_with_2_available_slots_after_are_shown()
    {
        $employee = Employee::factory()->create();

        $available1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id]);
        $reserved = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30),'end_time' => Carbon::today()->addHours(10),'employee_id' => $employee->id]);
        $available2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id]);
        $available3 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10)->addMinutes(30),'end_time' => Carbon::today()->addHours(11),'employee_id' => $employee->id]);
        $available4 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(11),'end_time' => Carbon::today()->addHours(11)->addMinutes(30),'employee_id' => $employee->id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 3;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

    /** @test */
    public function when_a_client_requests_many_services_with_summed_durations_requiring_4_slots_then_only_time_slots_with_3_available_slots_after_are_shown()
    {
        $employee = Employee::factory()->create();
        $available1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id]);
        $reserved = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30),'end_time' => Carbon::today()->addHours(10),'employee_id' => $employee->id]);
        $available2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id]);
        $available3 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10)->addMinutes(30),'end_time' => Carbon::today()->addHours(11),'employee_id' => $employee->id]);
        $available4 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(11),'end_time' => Carbon::today()->addHours(11)->addMinutes(30),'employee_id' => $employee->id]);
        $available5 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(11)->addMinutes(30),'end_time' => Carbon::today()->addHours(12),'employee_id' => $employee->id]);
        $available6 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(12),'end_time' => Carbon::today()->addHours(12)->addMinutes(30),'employee_id' => $employee->id]);
        $available7 = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(12)->addMinutes(30),'end_time' => Carbon::today()->addHours(13),'employee_id' => $employee->id]);
        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 4;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(2, $availableTimeSlots->count());
    }

    /** @test */
    public function specifying_employee_id_will_return_available_slots_for_only_that_particular_employee()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();

        $available1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee1->id]);
        $available2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30),'end_time' => Carbon::today()->addHours(10),'employee_id' => $employee1->id]);
        $available3 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee2->id]);
        $available4 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10)->addMinutes(30),'end_time' => Carbon::today()->addHours(11),'employee_id' => $employee2->id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($to, $from, $employee1->id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertFalse($availableTimeSlots->contains(function ($slot) use ($employee2) {
            return $slot['employee_id'] == $employee2->id;
        }));
    }

    /** @test */
    public function a_time_slot_only_counts_towards_consecutiveness_if_they_are_on_the_same_day()
    {
        $employee = Employee::factory()->create();
        $tsDay1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id]);
        $tsDay2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addDay()->addHours(14), 'end_time' => Carbon::today()->addDay()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(0, $availableTimeSlots->count());
    }

    /** @test */
    public function a_time_slot_only_counts_towards_consecutiveness_if_they_belong_to_the_same_employee()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();
        $tsEmployee1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee1->id]);
        $tsEmployee2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee2->id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(0, $availableTimeSlots->count());
    }

    /** @test */
    public function number_of_available_time_slots_is_0_when_nothing_is_available()
    {
        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(0, $availableTimeSlots->count());
    }

    // TODO: need to finish. need to set up second mysql testing db
    /** @test */
    public function time_slots_before_the_provided_date_from_will_not_be_retrieved()
    {
        $from = Carbon::today();
        $to = Carbon::today()->addDays(5);
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

    }

    /** @test */
    public function time_slots_after_the_provided_date_to_will_not_be_retrieved()
    {
        
    }

    /** @test */
    public function time_slots_retrieved_are_inclusive_of_date_from()
    {
        
    }

    /** @test */
    public function time_slots_retrieved_are_inclusive_of_date_to()
    {
        
    }

    /** @test */
    public function a_random_time_slot_is_retrieved_when_employee_is_not_specified_and_many_available_slots_exist_for_the_same_time()
    {
        
    }

    /** @test */
    public function only_one_time_slot_can_be_retrieved_per_date_time()
    {
        
    }
}
