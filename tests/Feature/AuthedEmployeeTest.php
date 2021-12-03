<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AuthedEmployeeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_authed_employee_account_can_be_retrieved(): void
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user, 'api');

        $response = $this->get("employee/authed");

        $response->assertOk();
        $this->assertEquals($employee->id, $response->json('data.employee.id'));
    }
}
