<?php

namespace Tests\Unit\Jobs\CreateEmployeeSchedules;

use App\Models\EmployeeSchedule;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTimeSlotsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function time_slots_can_be_created_from_an_employees_schedule()
    {
        $schedule = EmployeeSchedule::factory()->create();

        $timeSlots = $schedule->createTimeSlots();

        $this->assertCount(16, $timeSlots);
    }

    /** @test */
    public function the_first_created_time_slot_will_start_at_the_start_of_the_employees_schedule()
    {
        $schedule = EmployeeSchedule::factory()->create();

        $timeSlots = $schedule->createTimeSlots();

        $this->assertTrue($timeSlots->first()->start_time->eq( $schedule->start_time ));
    }

    /** @test */
    public function the_last_created_time_slot_will_end_at_the_end_of_the_employees_schedule()
    {
        $schedule = EmployeeSchedule::factory()->create();

        $timeSlots = $schedule->createTimeSlots();

        $this->assertTrue($timeSlots->last()->end_time->eq( $schedule->end_time ));
    }

    /** @test */
    public function the_last_time_slot_will_end_before_schedule_end_if_duration_of_time_slot_starts_before_work_day_ends_but_ends_after_work_day_starts()
    {
        $schedule = EmployeeSchedule::factory()->create(['end_time' => today()->addHours(17)->addMinutes(15)]);

        $timeSlots = $schedule->createTimeSlots();

        $this->assertTrue($timeSlots->last()->end_time->lt( $schedule->end_time ));
    }
}
