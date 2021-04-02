<?php

namespace Tests\Unit\Services\TimeSlot;

use App\Models\Company;
use App\Models\Employee;
use App\Models\TimeSlot;
use App\Services\TimeSlot\TimeSlotPdo;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiTimeSlotTest extends TestCase
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

        $available1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $reserved = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30),'end_time' => Carbon::today()->addHours(10),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available3 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10)->addMinutes(30),'end_time' => Carbon::today()->addHours(11),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

    /** @test */
    public function when_a_client_requests_many_services_with_summed_durations_requiring_3_slots_then_only_time_slots_with_2_available_slots_after_are_shown()
    {
        $employee = Employee::factory()->create();

        $available1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $reserved = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30),'end_time' => Carbon::today()->addHours(10),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available3 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10)->addMinutes(30),'end_time' => Carbon::today()->addHours(11),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available4 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(11),'end_time' => Carbon::today()->addHours(11)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 3;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

    /** @test */
    public function when_a_client_requests_many_services_with_summed_durations_requiring_4_slots_then_only_time_slots_with_3_available_slots_after_are_shown()
    {
        $employee = Employee::factory()->create();
        $available1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $reserved = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30),'end_time' => Carbon::today()->addHours(10),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available3 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10)->addMinutes(30),'end_time' => Carbon::today()->addHours(11),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available4 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(11),'end_time' => Carbon::today()->addHours(11)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available5 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(11)->addMinutes(30),'end_time' => Carbon::today()->addHours(12),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available6 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(12),'end_time' => Carbon::today()->addHours(12)->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $available7 = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(12)->addMinutes(30),'end_time' => Carbon::today()->addHours(13),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 4;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(2, $availableTimeSlots->count());
    }

    /** @test */
    public function specifying_employee_id_will_return_available_slots_for_only_that_particular_employee()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create(['company_id' => $employee1->company_id]);

        $available1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $available2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30),'end_time' => Carbon::today()->addHours(10),'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $available3 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);
        $available4 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10)->addMinutes(30),'end_time' => Carbon::today()->addHours(11),'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee1->company_id, $employee1->id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertFalse($availableTimeSlots->contains(function ($slot) use ($employee2) {
            return $slot['employee_id'] == $employee2->id;
        }));
    }

    /** @test */
    public function a_time_slot_only_counts_towards_consecutiveness_if_they_are_on_the_same_day()
    {
        $employee = Employee::factory()->create();
        $tsDay1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsDay2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addDay()->addHours(14), 'end_time' => Carbon::today()->addDay()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(0, $availableTimeSlots->count());
    }

    /** @test */
    public function a_time_slot_only_counts_towards_consecutiveness_if_they_belong_to_the_same_employee()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create(['company_id' => $employee1->company_id]);
        $tsEmployee1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $tsEmployee2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee1->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(0, $availableTimeSlots->count());
    }

    /** @test */
    public function number_of_available_time_slots_is_0_when_nothing_is_available()
    {
        $employee = Employee::factory()->create();
        $tsAvailable = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsReserved = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30), 'end_time' => Carbon::today()->addHours(10), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(0, $availableTimeSlots->count());
    }

    // TODO: need to finish. need to set up second mysql testing db
    /** @test */
    public function time_slots_before_the_provided_date_from_will_not_be_retrieved()
    {
        $employee = Employee::factory()->create();
        $tsAvailableOutOfRange1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->subDay()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableOutOfRange2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->subDay()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableInRange1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableInRange2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = Carbon::today();
        $to = Carbon::today()->addDays(5);
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

    /** @test */
    public function time_slots_after_the_provided_date_to_will_not_be_retrieved()
    {
        $employee = Employee::factory()->create();
        $tsAvailableOutOfRange1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addDays(6)->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableOutOfRange2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addDays(6)->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableInRange1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableInRange2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = Carbon::today();
        $to = Carbon::today()->addDays(5);
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

    /** @test */
    public function time_slots_retrieved_are_inclusive_of_date_from()
    {
        $from = Carbon::today();
        $to = Carbon::today()->addDays(5);
        $slotsRequired = 2;

        $employee = Employee::factory()->create();
        $tsExactDateFrom = TimeSlot::factory()->create(['start_time' => $from, 'end_time' => $from->copy()->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsNext = TimeSlot::factory()->create(['start_time' => $from->copy()->addMinutes(30), 'end_time' => $from->copy()->addHour(), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

    /** @test */
    public function time_slots_retrieved_are_inclusive_of_date_to()
    {
        $from = Carbon::today();
        $to = Carbon::today()->addDays(5)->addHours(2);
        $slotsRequired = 2;

        $employee = Employee::factory()->create();
        $tsBefore = TimeSlot::factory()->create(['start_time' => $to->copy()->subHour(), 'end_time' => $to->copy()->subMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsExactAsDateTo = TimeSlot::factory()->create(['start_time' => $to->copy()->subMinutes(30), 'end_time' => $to, 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

    /** @test */
    public function only_one_time_slot_can_be_retrieved_per_date_time()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create(['company_id' => $employee1->company_id]);

        $ts1Employee1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $ts2Employee1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30), 'end_time' => Carbon::today()->addHours(10), 'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $ts1Employee2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);
        $ts2Employee2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30), 'end_time' => Carbon::today()->addHours(10), 'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);

        $from = Carbon::today();
        $to = Carbon::today()->addDays(5);
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee1->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
    }

     /** @test */
     public function a_time_slot_query_is_scoped_to_a_single_company()
     {
         $company1 = Company::factory()->create();
         $company2 = Company::factory()->create();

         $employee = Employee::factory()->for($company1)->create();

         $ts1Company1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company1]);
         $ts2Company1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company1]);

         $ts1Company2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company2]);
         $ts1Company2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(14), 'end_time' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company2]);
 
         $from = Carbon::today();
         $to = $from->copy()->addMonth();
         $slotsRequired = 2;
 
         $tsPdo = new TimeSlotPdo($from, $to, $company1->id);
         $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);
 
         $this->assertEquals(1, $availableTimeSlots->count());
         $this->assertEquals($company1->id, $availableTimeSlots[0]['company_id']);
     }
}
