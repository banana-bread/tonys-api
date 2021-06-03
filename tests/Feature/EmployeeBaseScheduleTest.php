<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeBaseScheduleTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_can_update_their_base_schedule()
    {

    }

    /** @test */
    public function an_employee_cannot_update_other_employees_base_schedules()
    {

    }

    /** @test */
    public function an_owner_can_update_other_employees_base_schedules()
    {

    }

    /** @test */
    public function an_admin_can_update_other_employees_base_schedules()
    {

    }

    /** @test */
    public function reserved_slots_from_the_previous_schedule_will_stay_reserved_when_updated()
    {

    }

    /** @test */
    public function starting_time_cannot_be_before_company_starting_time()
    {

    }

    /** @test */
    public function ending_time_cannot_be_after_company_ending_time()
    {

    }

    /** @test */
    public function a_job_will_be_queued_when_base_schedule_is_updated()
    {

    }
}