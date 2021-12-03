<?php

namespace Tests\Unit\Models\Employee;

use App\Helpers\BaseSchedule;
use App\Models\Company;
use App\Models\Employee;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestUtils;

class BaseScheduleTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function falls_within_returns_true_if_base_schedule_starts_on_or_after_provided_base_schedule()
    {
        $employeeSchedule = TestUtils::mockBaseSchedule(9, 17);
        $companySchedule = TestUtils::mockBaseSchedule(9, 17);

        $fallsWithin = $employeeSchedule->fallsWithin($companySchedule);

        $this->assertTrue($fallsWithin);
    }

    /** @test */
    public function falls_within_returns_true_if_base_schedule_ends_on_or_before_provided_base_schedule()
    {
        $employeeSchedule = TestUtils::mockBaseSchedule(9, 17);
        $companySchedule = TestUtils::mockBaseSchedule(9, 17);

        $fallsWithin = $employeeSchedule->fallsWithin($companySchedule);

        $this->assertTrue($fallsWithin);
    }

    /** @test */
    public function falls_within_returns_false_if_base_schedule_starts_before_provided_base_schedule()
    {
        $employeeSchedule = TestUtils::mockBaseSchedule(8, 17);
        $companySchedule = TestUtils::mockBaseSchedule(9, 17);

        $fallsWithin = $employeeSchedule->fallsWithin($companySchedule);

        $this->assertFalse($fallsWithin);
    }

    /** @test */
    public function falls_within_returns_false_if_base_schedule_ends_after_provided_base_schedule()
    {
        $employeeSchedule = TestUtils::mockBaseSchedule(9, 18);
        $companySchedule = TestUtils::mockBaseSchedule(9, 17);

        $fallsWithin = $employeeSchedule->fallsWithin($companySchedule);

        $this->assertFalse($fallsWithin);
    }

    // TODO: these tests for matches dont work
    
    /** @test */
    public function matches_returns_true_if_base_schedules_are_exactly_the_same()
    {
        $schedule = TestUtils::mockBaseSchedule(9, 18);
        $updatedSchedule = TestUtils::mockBaseSchedule(9, 18);

        $matches = $schedule->matches($updatedSchedule);

        $this->assertTrue($matches);
    }

    /** @test */
    public function matches_returns_false_if_base_schedules_are_different()
    {
        $schedule = TestUtils::mockBaseSchedule(9, 18);
        $updatedSchedule = TestUtils::mockBaseSchedule(9, 19);

        $matches = $schedule->matches($updatedSchedule);

        $this->assertFalse($matches);
    }
}