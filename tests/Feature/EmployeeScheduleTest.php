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
    public function an_employee_can_update_their_base_schedule(): void
    {
        
    }

    /** @test */
    public function an_employee_can_add_exceptions_to_their_schedule(): void
    {

    }
}
