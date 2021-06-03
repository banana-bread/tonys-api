<?php

namespace Tests\Unit\Models\Employee;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSlotsForNextTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function time_slots_can_be_created()
    {
        $employee = Employee::factory()->create();

        $timeSlots = $employee->createSlotsForNext(10);

        $this->assertNotEmpty($timeSlots);
    }

    /** @test */
    public function the_first_created_time_slot_will_start_at_the_start_of_the_employees_base_schedule_time()
    {
        $employee = Employee::factory()->no_days_off()->create();

        $timeSlots = $employee->createSlotsForNext(1);

        $baseScheduleStart = $timeSlots->first()->start_time->copy()->startOfDay()->addSeconds($employee->base_schedule['monday']['start']);

        $this->assertTrue($timeSlots->first()->start_time->eq( $baseScheduleStart ));
    }

    /** @test */
    public function the_last_created_time_slot_will_end_at_the_end_of_the_employees_base_schedule_time()
    {
        $employee = Employee::factory()->no_days_off()->create();

        $timeSlots = $employee->createSlotsForNext(1);

        $baseScheduleEnd = $timeSlots->last()->end_time->copy()->startOfDay()->addSeconds($employee->base_schedule['monday']['end']);

        $this->assertTrue($timeSlots->last()->end_time->eq( $baseScheduleEnd ));
    }

    /** @test */
    public function the_last_created_time_slot_in_a_day_will_end_before_base_schedule_day_end_if_time_slot_would_start_before_but_end_after()
    {
        $employee = Employee::factory()->days_end_on_quarter_hour()->create();

        $timeSlots = $employee->createSlotsForNext(1);

        $baseScheduleEnd = $timeSlots->last()->end_time->copy()->startOfDay()->addSeconds($employee->base_schedule['monday']['end']);

        $this->assertTrue($timeSlots->last()->end_time->lt($baseScheduleEnd));
    }

    /** @test */
    public function time_slots_will_be_created_for_the_number_of_days_specified_if_no_days_are_off()
    {
        $employee = Employee::factory()->no_days_off()->create();
        $slotsInSingleDay = 16;
        $days = $this->faker->numberBetween(1, 20);

        $timeSlots = $employee->createSlotsForNext($days);

        $this->assertCount(($slotsInSingleDay * $days), $timeSlots);
    }

    /** @test */
    public function time_slots_will_not_be_created_for_number_of_days_specified_if_some_days_are_off()
    {
        $employee = Employee::factory()->create();
        $slotsInSingleDay = 16;
        $days = $this->faker->numberBetween(7, 20);

        $timeSlots = $employee->createSlotsForNext($days);

        $this->assertNotCount(($slotsInSingleDay * $days), $timeSlots);
    }

    // These next 2 test assume that an employee cannot alter their working hours in a day.  May add that in as a feature one day

    /** @test */
    public function an_employees_created_time_slots_will_start_the_day_after_the_latest_time_slot_if_slots_exist()
    {
        $employee = Employee::factory()->no_days_off()->create();
        $days = $this->faker->numberBetween(1, 50);


        $existingSlots = $employee->createSlotsForNext($days);
        $newSlots = $employee->createSlotsForNext($days);

        $latestExistingSlotDay = $existingSlots->last()->start_time->copy()->startOfDay();
        $firstNewSlotDay = $newSlots->first()->start_time->copy()->startOfDay();

        $this->assertTrue($latestExistingSlotDay->eq($firstNewSlotDay->subDay()));
    }

    /** @test */
    public function an_employees_created_time_slots_will_start_today_if_none_exist()
    {
        $employee = Employee::factory()->no_days_off()->create();
        $days = $this->faker->numberBetween(1, 50);

        $slots = $employee->createSlotsForNext($days);

        $this->assertTrue($slots->first()->start_time->isToday());
    }

    /** @test */
    public function an_employees_time_slots_will_not_be_created_if_days_are_not_working_days()
    {
        $employee = Employee::factory()->no_working_days()->create();
        $days = $this->faker->numberBetween(1, 50);

        $slots = $employee->createSlotsForNext($days);

        $this->assertEmpty($slots);
    }
    
}
