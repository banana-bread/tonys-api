<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeAdminTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_account_can_be_upgraded_to_admin()
    {
        $employee = Employee::factory()->create(); 
        $this->actingAs($employee->user, 'api');

        $response = $this->post("employee-admins/$employee->id");

        $response->assertCreated();
        $this->assertFalse($employee->admin);
        $this->assertTrue($response->json('data.employee.admin'));
    }

    /** @test */
    public function an_employee_account_can_be_downgraded_from_admin()
    {
        $employee = Employee::factory()->admin()->create(); 
        $this->actingAs($employee->user, 'api');

        $response = $this->delete("employee-admins/$employee->id");

        $response->assertStatus(204);
    }
}