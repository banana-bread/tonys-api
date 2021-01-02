<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class EmployeeScheduleTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_can_create_a_schedule(): void
    {

    }

    /** @test */
    public function an_employee_can_retrieve_their_schedule(): void
    {

    }

    /** @test */
    public function an_employee_can_update_their_predefined_schedule(): void
    {
        
    }

    /** @test */
    public function an_employee_can_add_vacation_days_to_their_predefined_schedule(): void
    {

    }
}
