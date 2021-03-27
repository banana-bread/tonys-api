<?php

namespace Tests\Unit\Models\TimeSlot;

use App\Models\Employee;
use App\Models\TimeSlot;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetNextSlotsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function getting_next_available_slot_when_2_total_slots_are_required_retrieves_only_one_slot()
    {
        $employee = Employee::factory()->create();
        $ts1 = TimeSlot::factory()->create([
            'end_time' => today()->addHours(9)->addMinutes(30),
            'start_time' => today()->addHours(9),
            'employee_id' => $employee->id,
        ]);
        $ts2 = TimeSlot::factory()->create([
            'start_time' => today()->addHours(9)->addMinutes(30),
            'end_time' => today()->addHours(10),
            'employee_id' => $employee->id,
        ]);
        $totalSlotsRequied = 2;

        $nextAvailableSlots = $ts1->getNextSlots($totalSlotsRequied);

        $this->assertEquals(1, $nextAvailableSlots->count());
    }

    /** @test */
    public function getting_next_available_slot_when_3_total_slots_are_required_retrieves_2_slots()
    {
        $employee = Employee::factory()->create();
        $ts1 = TimeSlot::factory()->create([
            'end_time' => today()->addHours(9)->addMinutes(30),
            'start_time' => today()->addHours(9),
            'employee_id' => $employee->id,
        ]);
        $ts2 = TimeSlot::factory()->create([
            'start_time' => today()->addHours(9)->addMinutes(30),
            'end_time' => today()->addHours(10),
            'employee_id' => $employee->id,
        ]);
        $ts3 = TimeSlot::factory()->create([
            'start_time' => today()->addHours(10),
            'end_time' => today()->addHours(10)->addMinutes(30),
            'employee_id' => $employee->id,
        ]);
        $totalSlotsRequied = 3;

        $nextAvailableSlots = $ts1->getNextSlots($totalSlotsRequied);

        $this->assertEquals(2, $nextAvailableSlots->count());
    }

    /** @test */ 
    public function next_slot_is_not_retrieved_if_it_is_on_the_following_day() 
    {
        $employee = Employee::factory()->create();
        $ts1 = TimeSlot::factory()->create([
            'end_time' => today()->addHours(9)->addMinutes(30),
            'start_time' => today()->addHours(9),
            'employee_id' => $employee->id,
        ]);
        $ts2 = TimeSlot::factory()->create([
            'start_time' => today()->addDay()->addHours(9)->addMinutes(30),
            'end_time' => today()->addDay()->addHours(10),
            'employee_id' => $employee->id,
        ]);
        $totalSlotsRequied = 2;

        $nextAvailableSlots = $ts1->getNextSlots($totalSlotsRequied);

        $this->assertEquals(0, $nextAvailableSlots->count());
    }

    /** @test */ 
    public function next_slot_retrieved_will_belong_to_the_same_employee() 
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();
        $ts1 = TimeSlot::factory()->create([
            'end_time' => today()->addHours(9)->addMinutes(30),
            'start_time' => today()->addHours(9),
            'employee_id' => $employee1->id,
        ]);
        $ts2 = TimeSlot::factory()->create([
            'start_time' => today()->addDay()->addHours(9)->addMinutes(30),
            'end_time' => today()->addDay()->addHours(10),
            'employee_id' => $employee2->id,
        ]);
        $totalSlotsRequied = 2;

        $nextAvailableSlots = $ts1->getNextSlots($totalSlotsRequied);

        $this->assertEquals(0, $nextAvailableSlots->count());
    }

    /** @test */ 
    public function next_slot_retrieved_will_be_immediately_after_the_starting_slot() 
    {
        $employee = Employee::factory()->create();
        $ts1 = TimeSlot::factory()->create([
            'end_time' => today()->addHours(9)->addMinutes(30),
            'start_time' => today()->addHours(9),
            'employee_id' => $employee->id,
        ]);
        $ts2 = TimeSlot::factory()->create([
            'start_time' => today()->addHours(9)->addMinutes(30),
            'end_time' => today()->addHours(10),
            'employee_id' => $employee->id,
        ]);
        $totalSlotsRequied = 2;

        $nextAvailableSlots = $ts1->getNextSlots($totalSlotsRequied);

        $this->assertEquals($ts1->end_time, $nextAvailableSlots->first()->start_time);
    }

    






}
