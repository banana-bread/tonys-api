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
use Tests\MocksTimeSlots;

class MultiTimeSlotTest extends TestCase
{
    use WithFaker, RefreshDatabase, MocksTimeSlots;

    // This may still be useful for potenatial feature tests
    // $response = $this->get("/time-slots?"
    //     ."service-definition-ids=$s1->id,$s2->id&"
    //     ."employee-id=$e->id&"
    //     ."date-from=$from&"
    //     ."date-to=$to");

    // ⨯ when a client requests many services with summed durations requiring 2 slots then only time slots with an available slot after are shown
    // ⨯ when a client requests many services with summed durations requiring 3 slots then only time slots with 2 available slots after are shown
    // ⨯ when a client requests many services with summed durations requiring 4 slots then only time slots with 3 available slots after are shown
    // ⨯ time slots after the provided date to will not be retrieved
    // ⨯ only one time slot can be retrieved per date time
    // ⨯ a time slot query is scoped to a single company

    // /** @test */
    // public function when_a_client_requests_many_services_with_summed_durations_requiring_2_slots_then_only_time_slots_with_an_available_slot_after_are_shown()
    // {
    //     $employee = Employee::factory()->no_days_off()->create();

    //     $available1 = TimeSlot::factory()->create(['start_time' => now(),'end_time' => now()->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $reserved = TimeSlot::factory()->reserved()->create(['start_time' => now()->addMinutes(30),'end_time' => now()->addMinutes(60),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(60),'end_time' => now()->addMinutes(90),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available3 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(90),'end_time' => now()->addMinutes(120),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

    //     $from = today();
    //     $to = $from->copy()->addMonth();
    //     $slotsRequired = 2;

    //     $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
    //     $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

    //     $this->assertEquals(1, $availableTimeSlots->count());
    // }

    // /** @test */
    // public function when_a_client_requests_many_services_with_summed_durations_requiring_3_slots_then_only_time_slots_with_2_available_slots_after_are_shown()
    // {
    //     $employee = Employee::factory()->no_days_off()->create();

    //     $available1 = TimeSlot::factory()->create(['start_time' => today()->addMinutes(30),'end_time' => today()->addMinutes(60),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $reserved = TimeSlot::factory()->reserved()->create(['start_time' => today()->addMinutes(60),'end_time' => today()->addMinutes(90),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available2 = TimeSlot::factory()->create(['start_time' => today()->addMinutes(90),'end_time' => today()->addMinutes(120),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available3 = TimeSlot::factory()->create(['start_time' => today()->addMinutes(120),'end_time' => today()->addMinutes(150),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available4 = TimeSlot::factory()->create(['start_time' => today()->addMinutes(150),'end_time' => today()->addMinutes(180),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

    //     Carbon::setTestNow(today());

    //     $from = today()->subDay();
    //     $to = $from->copy()->addMonth();
    //     $slotsRequired = 3;

    //     $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
    //     $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

    //     $this->assertEquals(1, $availableTimeSlots->count());
    // }

    // /** @test */
    // public function when_a_client_requests_many_services_with_summed_durations_requiring_4_slots_then_only_time_slots_with_3_available_slots_after_are_shown()
    // {
    //     $employee = Employee::factory()->create();
    //     $available1 = TimeSlot::factory()->create(['start_time' => now(),'end_time' => now()->addMinutes(30),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $reserved = TimeSlot::factory()->reserved()->create(['start_time' => now()->addMinutes(30),'end_time' => now()->addMinutes(60),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(60),'end_time' => now()->addMinutes(90),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available3 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(90),'end_time' => now()->addMinutes(120),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available4 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(120),'end_time' => now()->addMinutes(150),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available5 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(150),'end_time' => now()->addMinutes(180),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available6 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(180),'end_time' => now()->addMinutes(210),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $available7 = TimeSlot::factory()->reserved()->create(['start_time' => now()->addMinutes(210),'end_time' => now()->addMinutes(240),'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $from = today();
    //     $to = $from->copy()->addMonth();
    //     $slotsRequired = 4;

    //     $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
    //     $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

    //     $this->assertEquals(2, $availableTimeSlots->count());
    // }

    /** @test */
    public function specifying_employee_id_will_return_available_slots_for_only_that_particular_employee()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create(['company_id' => $employee1->company_id]);

        $available1 = TimeSlot::factory()->create(['start_time' => now(),'end_time' => now()->addMinutes(30),'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $available2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(30),'end_time' => now()->addMinutes(60),'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $available3 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(60),'end_time' => now()->addMinutes(90),'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);
        $available4 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(90),'end_time' => now()->addMinutes(120),'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);

        $from = today();
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
        $tsDay1 = TimeSlot::factory()->create(['start_time' => now(), 'end_time' => now()->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsDay2 = TimeSlot::factory()->create(['start_time' => now()->addDay()->addMinutes(30), 'end_time' => now()->addDay()->addMinutes(60), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        // Carbon::setTestNow(now()->addDay());

        $from = today();
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
        $tsEmployee1 = TimeSlot::factory()->create(['start_time' => now(), 'end_time' => now()->addMinutes(30), 'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $tsEmployee2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(30), 'end_time' => now()->addMinutes(60), 'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);

        $from = today();
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
        $tsAvailable = TimeSlot::factory()->create(['start_time' => today()->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsReserved = TimeSlot::factory()->reserved()->create(['start_time' => today()->addHours(9)->addMinutes(30), 'end_time' => today()->addHours(10), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(0, $availableTimeSlots->count());
    }

    /** @test */

    public function time_slots_after_the_provided_date_to_will_not_be_retrieved()
    {
        $employee = Employee::factory()->create();
        $tsAvailableOutOfRange1 = TimeSlot::factory()->create(['start_time' => today()->addDays(6)->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableOutOfRange2 = TimeSlot::factory()->create(['start_time' => today()->addDays(6)->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableInRange1 = TimeSlot::factory()->create(['start_time' => today()->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableInRange2 = TimeSlot::factory()->create(['start_time' => today()->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = today();
        $to = today()->addDays(5);
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertCount(1, $availableTimeSlots);
    }

    // /** @test */
    // public function time_slots_retrieved_are_inclusive_of_date_from()
    // {
    //     $from = now();
    //     $to = now()->addDays(5);
    //     $slotsRequired = 2;

    //     $employee = Employee::factory()->create();
    //     $tsExactDateFrom = TimeSlot::factory()->create(['start_time' => $from, 'end_time' => $from->copy()->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
    //     $tsNext = TimeSlot::factory()->create(['start_time' => $from->copy()->addMinutes(30), 'end_time' => $from->copy()->addHour(), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

    //     $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
    //     $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

    //     $this->assertCount(1, $availableTimeSlots);
    // }

    /** @test */
    public function time_slots_retrieved_are_inclusive_of_date_to()
    {
        $from = today();
        $to = today()->addDays(5)->addHours(2);
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

        $ts1Employee1 = TimeSlot::factory()->create(['start_time' => today()->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $ts2Employee1 = TimeSlot::factory()->create(['start_time' => today()->addHours(9)->addMinutes(30), 'end_time' => today()->addHours(10), 'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $ts1Employee2 = TimeSlot::factory()->create(['start_time' => today()->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);
        $ts2Employee2 = TimeSlot::factory()->create(['start_time' => today()->addHours(9)->addMinutes(30), 'end_time' => today()->addHours(10), 'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);

        $from = today();
        $to = today()->addDays(5);
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

        $ts1Company1 = TimeSlot::factory()->create(['start_time' => today()->addHours(14), 'end_time' => today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company1]);
        $ts2Company1 = TimeSlot::factory()->create(['start_time' => today()->addHours(14), 'end_time' => today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company1]);

        $ts1Company2 = TimeSlot::factory()->create(['start_time' => today()->addHours(14), 'end_time' => today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company2]);
        $ts1Company2 = TimeSlot::factory()->create(['start_time' => today()->addHours(14), 'end_time' => today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company2]);

        $from = today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $company1->id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(1, $availableTimeSlots->count());
        $this->assertEquals($company1->id, $availableTimeSlots[0]['company_id']);
    }

    /** @test */
    public function a_time_slot_will_not_be_retreived_if_its_start_time_has_passed()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->create();

        $ts1 = TimeSlot::factory()->create(['start_time' => now()->subMinute(), 'end_time' => now()->addMinutes(29), 'employee_id' => $employee->id, 'company_id' => $company]);
        $ts2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(29), 'end_time' => now()->addMinutes(59), 'employee_id' => $employee->id, 'company_id' => $company]);
        $ts3 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(59), 'end_time' => now()->addMinutes(89), 'employee_id' => $employee->id, 'company_id' => $company]);

        $from = today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $company->id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertCount(1, $availableTimeSlots);
    }

    /** @test */
    public function a_time_slot_will_not_be_retreived_if_now_plus_booking_grace_period_is_greater_than_its_start_time()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->create();

        $ts1 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(14), 'end_time' => now()->addMinutes(44), 'employee_id' => $employee->id, 'company_id' => $company]);
        $ts2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(44), 'end_time' => now()->addMinutes(74), 'employee_id' => $employee->id, 'company_id' => $company]);
        $ts3 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(104), 'end_time' => now()->addMinutes(134), 'employee_id' => $employee->id, 'company_id' => $company]);
        
        $from = today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($from, $to, $company->id);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertCount(1, $availableTimeSlots);
    }

    /** @test */
   public function non_active_employee_time_slots_will_not_be_retreived()
   {
    $company = Company::factory()->create();
    $employee = Employee::factory()->for($company)->inactive()->create();

    $ts1 = TimeSlot::factory()->create(['start_time' => now(), 'end_time' => now()->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company]);
    $ts2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(30), 'end_time' => now()->addMinutes(60), 'employee_id' => $employee->id, 'company_id' => $company]);
    $ts3 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(60), 'end_time' => now()->addMinutes(90), 'employee_id' => $employee->id, 'company_id' => $company]);
    
    $from = today();
    $to = $from->copy()->addMonth();
    $slotsRequired = 2;

    $tsPdo = new TimeSlotPdo($from, $to, $company->id);
    $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

    $this->assertCount(0, $availableTimeSlots);
   }
}
