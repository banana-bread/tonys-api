<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeAdminTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    // TODO: Finish empty tests and create auth policies
    /** @test */
    public function an_employee_account_can_be_upgraded_to_admin()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->for($owner->company)->create(); 
        $this->actingAs($owner->user, 'api');

        $response = $this->post("/locations/$employee->company_id/employees/$employee->id/admin");

        $response->assertCreated();
        $this->assertFalse($employee->admin);
        $this->assertTrue($response->json('data.employee.admin'));
    }

    /** @test */
    public function only_owners_can_upgrade_employees_to_admin()
    {

    }

    /** @test */
    public function an_employee_account_can_be_downgraded_from_admin()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->for($owner->company)->admin()->create(); 
        $this->actingAs($owner->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/employees/$employee->id/admin");

        $response->assertStatus(204);
    }


    /** @test */
    public function only_owners_can_downgrade_employees_from_admin()
    {
  
    }
}