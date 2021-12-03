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

class SingleTimeSlotTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function specifying_employee_id_will_return_available_slots_for_only_that_particular_employee()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create(['company_id' => $employee1->company_id]);

        $tsEmployee1 = TimeSlot::factory()->create(['start_time' => today()->addHours(9),'end_time' => today()->addHours(9)->addMinutes(30),'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
        $tsEmployee2 = TimeSlot::factory()->create(['start_time' => today()->addHours(10),'end_time' => today()->addHours(10)->addMinutes(30),'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);

        $from = today();
        $to = $from->copy()->addMonth();

        $tsPdo = new TimeSlotPdo($from, $to, $employee1->company_id, $employee1->id);
        $availableTimeSlots = $tsPdo->fetchAvailableSlots();

        $this->assertFalse($availableTimeSlots->contains(function ($slot) use ($employee2) {
            return $slot['employee_id'] == $employee2->id;
        }));
    }

    /** @test */
    public function number_of_available_time_slots_is_0_when_nothing_is_available()
    {
        $tsReserved = TimeSlot::factory()->reserved()->create(['start_time' => today()->addHours(9)->addMinutes(30), 'end_time' => today()->addHours(10)]);

        $from = today();
        $to = $from->copy()->addMonth();

        $tsPdo = new TimeSlotPdo($from, $to, $tsReserved->company_id);
        $availableTimeSlots = $tsPdo->fetchAvailableSlots();

        $this->assertEquals(0, $availableTimeSlots->count());
    }

     /** @test */
     public function only_one_time_slot_can_be_retrieved_per_date_time()
     {
         $employee1 = Employee::factory()->create();
         $employee2 = Employee::factory()->create(['company_id' => $employee1->company_id]);
 
         $tsEmployee1 = TimeSlot::factory()->create(['start_time' => today()->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee1->id, 'company_id' => $employee1->company_id]);
         $tsEmployee2 = TimeSlot::factory()->create(['start_time' => today()->addHours(9), 'end_time' => today()->addHours(9)->addMinutes(30), 'employee_id' => $employee2->id, 'company_id' => $employee1->company_id]);
 
         $from = today();
         $to = today()->addDays(5);
 
         $tsPdo = new TimeSlotPdo($from, $to, $employee1->company_id);
         $availableTimeSlots = $tsPdo->fetchAvailableSlots();
 
         $this->assertEquals(1, $availableTimeSlots->count());
     }

    /** @test */
    public function a_time_slot_query_is_scoped_to_a_single_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $employee = Employee::factory()->for($company1)->create();

        $tsCompany1 = TimeSlot::factory()->create(['start_time' => today()->addHours(14), 'end_time' => today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company1]);
        $tsCompany2 = TimeSlot::factory()->create(['start_time' => today()->addHours(14), 'end_time' => today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company2]);

        $from = today();
        $to = $from->copy()->addMonth();

        $tsPdo = new TimeSlotPdo($from, $to, $company1->id);
        $availableTimeSlots = $tsPdo->fetchAvailableSlots();

        $this->assertEquals(1, $availableTimeSlots->count());
        $this->assertEquals($company1->id, $availableTimeSlots[0]['company_id']);
    }

    /** @test */
    public function time_slots_after_the_provided_date_from_will_not_be_retrieved()
    {        
        $employee = Employee::factory()->create();
        $tsAvailableOutOfRange = TimeSlot::factory()->create(['start_time' => today()->addDays(6)->addHours(9), 'end_time' => today()->addDays(6)->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);
        $tsAvailableInRange = TimeSlot::factory()->create(['start_time' => today()->addDay()->addHours(9), 'end_time' => today()->addDay()->addHours(9)->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $employee->company_id]);

        $from = today();
        $to = today()->addDays(5);

        $tsPdo = new TimeSlotPdo($from, $to, $employee->company_id);
        $availableTimeSlots = $tsPdo->fetchAvailableSlots();

        $this->assertCount(1, $availableTimeSlots);
    }

    /** @test */
    public function time_slots_that_have_already_started_will_not_be_retrieved()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->create();
        $from = today();
        $to = $from->copy()->addMonth();

        $ts1 = TimeSlot::factory()->create(['start_time' => now()->subMinute(), 'end_time' => now()->addMinutes(29), 'employee_id' => $employee->id, 'company_id' => $company]);
        $ts2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(29), 'end_time' => now()->addMinutes(59), 'employee_id' => $employee->id, 'company_id' => $company]);

        $tsPdo = new TimeSlotPdo($from, $to, $company->id);
        $availableTimeSlots = $tsPdo->fetchAvailableSlots();

        $this->assertCount(1, $availableTimeSlots);
    }

    // This is currently hard-coded to 15 minutes but will need to be changed when adding more companies
    /** @test */
    public function a_time_slot_will_not_be_pulled_that_starts_before_booking_grace_period()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->create();
        $from = today();
        $to = $from->copy()->addMonth();

        $ts1 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(14), 'end_time' => now()->addMinutes(44), 'employee_id' => $employee->id, 'company_id' => $company]);
        $ts2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(44), 'end_time' => now()->addMinutes(74), 'employee_id' => $employee->id, 'company_id' => $company]);

        $tsPdo = new TimeSlotPdo($from, $to, $company->id);
        $availableTimeSlots = $tsPdo->fetchAvailableSlots();

        $this->assertCount(1, $availableTimeSlots);
    }

    public function non_active_employee_time_slots_will_not_be_retreived()
   {
    $company = Company::factory()->create();
    $employee = Employee::factory()->for($company)->inactive()->create();
    $from = today();
    $to = $from->copy()->addMonth();

    $ts1 = TimeSlot::factory()->create(['start_time' => now()->subMinute(), 'end_time' => now()->addMinutes(29), 'employee_id' => $employee->id, 'company_id' => $company]);
    $ts2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(29), 'end_time' => now()->addMinutes(59), 'employee_id' => $employee->id, 'company_id' => $company]);

    $tsPdo = new TimeSlotPdo($from, $to, $company->id);
    $availableTimeSlots = $tsPdo->fetchAvailableSlots();

    $this->assertCount(0, $availableTimeSlots);
   }
}