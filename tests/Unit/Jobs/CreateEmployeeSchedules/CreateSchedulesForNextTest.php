<?php

namespace Tests\Unit\Jobs\CreateEmployeeSchedules;

use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSchedulesForNextTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_schedule_will_be_created_for_the_number_of_days_specified()
    {
        $employee = Employee::factory()->create();
        $days = 10;

        $schedules = $employee->createSchedulesForNext($days);

        $this->assertCount($days, $schedules);
    }

    /** @test */
    public function an_employees_schedules_will_be_created_for_the_given_number_of_days()
    {
        $employee = Employee::factory()->create();
        $days = 10;

        $schedules = $employee->createSchedulesForNext($days);

        $this->assertCount($days, $schedules);
    }

    /** @test */
    public function an_employees_created_schedules_will_start_the_day_after_the_latest_schedule_if_one_exists()
    {

        $employee = Employee::factory()->create();
        EmployeeSchedule::factory()->create([
            'start_time' => today()->addHours(9),
            'end_time' => today()->addHours(17),
        ]);
        $latest = EmployeeSchedule::factory()->create([
            'start_time' => today()->addDay()->addHours(9),
            'end_time' => today()->addDay()->addHours(17),
        ]);
        $days = 10;

        $schedules = $employee->createSchedulesForNext($days);

        $firstFromCreatedSchedules = $schedules->first()->start_time->copy()->startOfDay();
        $latestFromExistingSchedules = $latest->start_time->copy()->startOfDay();

        $this->assertTrue($latestFromExistingSchedules->eq($firstFromCreatedSchedules->addDay()));
    }

    /** @test */
    public function an_employees_created_schedules_will_start_today_if_no_schedules_exist()
    {
        $employee = Employee::factory()->create();
        $days = 10;

        $schedules = $employee->createSchedulesForNext($days);

        $this->assertTrue($schedules->first()->start_time->startOfDay()->eq(today()));
    }

    // TODO: implement when/if system accepts holidays
    // public function an_employees_schedule_start_and_end_time_will_be_start_of_day_if_day_is_holiday()

    /** @test */ 
    public function an_employees_schedule_start_and_end_time_will_be_start_of_day_if_day_is_weekend()
    {
        $employee = Employee::factory()->create();
        $days = 10;

        $schedules = $employee->createSchedulesForNext($days);
        $weekendDay = $schedules->firstWhere('weekend', true);

        $this->assertTrue($weekendDay->start_time->eq( $weekendDay->start_time->startOfDay() ));
    }
}
