<?php

namespace Tests\Feature;
use App\Models\ServiceDefinition;
use App\Models\Employee;
use App\Models\TimeSlot;
use App\Services\TimeSlot\TimeSlotPdo;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimeSlotTest extends TestCase
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

        $available1 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(9),'ended_at' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id]);
        $reserved = TimeSlot::factory()->reserved()->create(['started_at' => Carbon::today()->addHours(9)->addMinutes(30),'ended_at' => Carbon::today()->addHours(10),'employee_id' => $employee->id]);
        $available2 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10),'ended_at' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id]);
        $available3 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10)->addMinutes(30),'ended_at' => Carbon::today()->addHours(11),'employee_id' => $employee->id]);

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

        $available1 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(9),'ended_at' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id]);
        $reserved = TimeSlot::factory()->reserved()->create(['started_at' => Carbon::today()->addHours(9)->addMinutes(30),'ended_at' => Carbon::today()->addHours(10),'employee_id' => $employee->id]);
        $available2 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10),'ended_at' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id]);
        $available3 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10)->addMinutes(30),'ended_at' => Carbon::today()->addHours(11),'employee_id' => $employee->id]);
        $available4 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(11),'ended_at' => Carbon::today()->addHours(11)->addMinutes(30),'employee_id' => $employee->id]);

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
        $available1 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(9),'ended_at' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee->id]);
        $reserved = TimeSlot::factory()->reserved()->create(['started_at' => Carbon::today()->addHours(9)->addMinutes(30),'ended_at' => Carbon::today()->addHours(10),'employee_id' => $employee->id]);
        $available2 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10),'ended_at' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee->id]);
        $available3 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10)->addMinutes(30),'ended_at' => Carbon::today()->addHours(11),'employee_id' => $employee->id]);
        $available4 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(11),'ended_at' => Carbon::today()->addHours(11)->addMinutes(30),'employee_id' => $employee->id]);
        $available5 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(11)->addMinutes(30),'ended_at' => Carbon::today()->addHours(12),'employee_id' => $employee->id]);
        $available6 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(12),'ended_at' => Carbon::today()->addHours(12)->addMinutes(30),'employee_id' => $employee->id]);
        $available7 = TimeSlot::factory()->reserved()->create(['started_at' => Carbon::today()->addHours(12)->addMinutes(30),'ended_at' => Carbon::today()->addHours(13),'employee_id' => $employee->id]);
        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 4;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(2, $availableTimeSlots->count());
    }


    /** @test */
    public function not_specifying_employee_id_will_return_consecutive_available_slots_for_all_employees()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();

        $available1 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(9),'ended_at' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee1->id]);
        $available2 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(9)->addMinutes(30),'ended_at' => Carbon::today()->addHours(10),'employee_id' => $employee1->id]);
        $available3 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10),'ended_at' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee2->id]);
        $available4 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10)->addMinutes(30),'ended_at' => Carbon::today()->addHours(11),'employee_id' => $employee2->id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertTrue($availableTimeSlots->contains(function ($slot) use ($employee1) {
            return $slot['employee_id'] == $employee1->id;
        }));

        $this->assertTrue($availableTimeSlots->contains(function ($slot) use ($employee2) {
            return $slot['employee_id'] == $employee2->id;
        }));
    }

    /** @test */
    public function specifying_employee_id_will_return_available_slots_for_only_that_particular_employee()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();

        $available1 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(9),'ended_at' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee1->id]);
        $available2 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(9)->addMinutes(30),'ended_at' => Carbon::today()->addHours(10),'employee_id' => $employee1->id]);
        $available3 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10),'ended_at' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee2->id]);
        $available4 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(10)->addMinutes(30),'ended_at' => Carbon::today()->addHours(11),'employee_id' => $employee2->id]);

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
        $tsDay1 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(14), 'ended_at' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id]);
        $tsDay2 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addDay()->addHours(14), 'ended_at' => Carbon::today()->addDay()->addHours(14)->addMinutes(30), 'employee_id' => $employee->id]);

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
        $tsEmployee1 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(14), 'ended_at' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee1->id]);
        $tsEmployee2 = TimeSlot::factory()->create(['started_at' => Carbon::today()->addHours(14), 'ended_at' => Carbon::today()->addHours(14)->addMinutes(30), 'employee_id' => $employee2->id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();
        $slotsRequired = 2;

        $tsPdo = new TimeSlotPdo($to, $from);
        $availableTimeSlots = $tsPdo->fetchConsecutiveAvailableSlots($slotsRequired);

        $this->assertEquals(0, $availableTimeSlots->count());
    }
}
