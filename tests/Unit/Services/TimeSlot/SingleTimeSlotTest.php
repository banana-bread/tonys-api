<?php

namespace Tests\Unit\Services\TimeSlot;

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
        $employee2 = Employee::factory()->create();

        $tsEmployee1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9),'end_time' => Carbon::today()->addHours(9)->addMinutes(30),'employee_id' => $employee1->id]);
        $tsEmployee2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(10),'end_time' => Carbon::today()->addHours(10)->addMinutes(30),'employee_id' => $employee2->id]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();

        $tsPdo = new TimeSlotPdo($from, $to, $employee1->id);
        $availableTimeSlots = $tsPdo->fetchAvailableSlots();

        $this->assertFalse($availableTimeSlots->contains(function ($slot) use ($employee2) {
            return $slot['employee_id'] == $employee2->id;
        }));
    }

    /** @test */
    public function number_of_available_time_slots_is_0_when_nothing_is_available()
    {
        $tsReserved = TimeSlot::factory()->reserved()->create(['start_time' => Carbon::today()->addHours(9)->addMinutes(30), 'end_time' => Carbon::today()->addHours(10)]);

        $from = Carbon::today();
        $to = $from->copy()->addMonth();

        $tsPdo = new TimeSlotPdo($from, $to);
        $availableTimeSlots = $tsPdo->fetchAvailableSlots();
        \Log::info($availableTimeSlots);

        $this->assertEquals(0, $availableTimeSlots->count());
    }

     /** @test */
     public function only_one_time_slot_can_be_retrieved_per_date_time()
     {
         $employee1 = Employee::factory()->create();
         $employee2 = Employee::factory()->create();
 
         $tsEmployee1 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee1->id]);
         $tsEmployee2 = TimeSlot::factory()->create(['start_time' => Carbon::today()->addHours(9), 'end_time' => Carbon::today()->addHours(9)->addMinutes(30), 'employee_id' => $employee2->id]);
 
         $from = Carbon::today();
         $to = Carbon::today()->addDays(5);
 
         $tsPdo = new TimeSlotPdo($from, $to);
         $availableTimeSlots = $tsPdo->fetchAvailableSlots();
 
         $this->assertEquals(1, $availableTimeSlots->count());
     }
}