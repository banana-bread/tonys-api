<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeOwnerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_account_can_be_upgraded_to_owner()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->for($owner->company)->create(); 
        $this->actingAs($owner->user, 'api');

        $response = $this->post("/locations/$employee->company_id/employees/$employee->id/owner");

        $response->assertCreated();
        $this->assertFalse($employee->owner);
        $this->assertTrue($response->json('data.employee.owner'));
    }

    /** @test */
    public function an_employee_upgraded_to_owner_will_also_be_upgraded_to_admin()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->for($owner->company)->create(); 
        $this->actingAs($owner->user, 'api');

        $response = $this->post("/locations/$employee->company_id/employees/$employee->id/owner");

        $this->assertFalse($employee->admin);
        $this->assertFalse($employee->owner);
        $this->assertTrue($response->json('data.employee.admin'));
        $this->assertTrue($response->json('data.employee.owner'));
    }

    /** @test */
    public function employees_cannot_upgrade_employees_to_owner()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->for($employee1->company)->create();
        $this->actingAs($employee1->user, 'api');

        $response = $this->post("/locations/$employee1->company_id/employees/$employee2->id/owner");

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_cannot_upgrade_employees_to_owner()
    {
        $admin = Employee::factory()->admin()->create();
        $employee2 = Employee::factory()->for($admin->company)->create();
        $this->actingAs($admin->user, 'api');

        $response = $this->post("/locations/$admin->company_id/employees/$employee2->id/owner");

        $response->assertStatus(403);
    }

    /** @test */
    public function an_employee_account_can_be_downgraded_from_owner()
    {
        $owner1 = Employee::factory()->owner()->create();
        $owner2 = Employee::factory()->for($owner1->company)->owner()->create(); 
        $this->actingAs($owner1->user, 'api');

        $response = $this->delete("/locations/$owner2->company_id/employees/$owner2->id/owner");

        $response->assertStatus(204);
        $this->assertFalse(Employee::find($owner2->id)->owner);
    }

    /** @test */
    public function employees_cannot_downgrade_employees_from_owner()
    {
        $employee = Employee::factory()->create();
        $owner = Employee::factory()->for($employee->company)->owner()->create(); 
        $this->actingAs($employee->user, 'api');

        $response = $this->delete("/locations/$owner->company_id/employees/$owner->id/owner");

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_cannot_downgrade_employees_from_owner()
    {
        $admin = Employee::factory()->admin()->create();
        $owner = Employee::factory()->for($admin->company)->owner()->create(); 
        $this->actingAs($admin->user, 'api');

        $response = $this->delete("/locations/$owner->company_id/employees/$owner->id/owner");

        $response->assertStatus(403);
    }

    /** @test */
    public function an_employee_cannot_be_downgraded_from_owner_if_no_other_owners_exist()
    {
        $owner = Employee::factory()->owner()->create();
        $this->actingAs($owner->user, 'api');

        $response = $this->delete("/locations/$owner->company_id/employees/$owner->id/owner");
        $response->assertStatus(400);
    }

    /** @test */
    public function an_owner_cannot_downgrade_employees_belonging_to_different_companies()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->create();
        $this->actingAs($owner->user, 'api');

        $response = $this->delete("/locations/$owner->company_id/employees/$employee->id/owner");

        $response->assertStatus(500);
    }

    /** @test */
    public function an_owner_cannot_upgrade_employees_belonging_to_different_companies()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->create();
        $this->actingAs($owner->user, 'api');

        $response = $this->post("/locations/$owner->company_id/employees/$employee->id/owner");

        $response->assertStatus(500);
    }
}
