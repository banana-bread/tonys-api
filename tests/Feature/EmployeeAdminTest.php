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
    public function employees_cannot_upgrade_employees_to_admin()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->for($employee1->company)->create();
        $this->actingAs($employee1->user, 'api');

        $response = $this->post("/locations/$employee1->company_id/employees/$employee2->id/admin");

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_cannot_upgrade_employees_to_admin()
    {
        $admin = Employee::factory()->admin()->create();
        $employee = Employee::factory()->for($admin->company)->create();
        $this->actingAs($admin->user, 'api');

        $response = $this->post("/locations/$employee->company_id/employees/$employee->id/admin");

        $response->assertStatus(403);
    }

    /** @test */
    public function an_employee_account_can_be_downgraded_from_admin()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->for($owner->company)->admin()->create(); 
        $this->actingAs($owner->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/employees/$employee->id/admin");

        $response->assertStatus(204);
        $this->assertFalse(Employee::find($employee->id)->admin);
    }


    /** @test */
    public function employees_cannot_downgrade_employees_from_admin()
    {
        $employee1 = Employee::factory()->admin()->create();
        $employee2 = Employee::factory()->for($employee1->company)->admin()->create();
        $this->actingAs($employee1->user, 'api');

        $response = $this->delete("/locations/$employee1->company_id/employees/$employee2->id/admin");

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_cannot_downgrade_employees_from_admin()
    {
        $admin = Employee::factory()->admin()->create();
        $employee = Employee::factory()->for($admin->company)->admin()->create();
        $this->actingAs($admin->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/employees/$employee->id/admin");

        $response->assertStatus(403);
    }

    /** @test */
    public function if_an_admin_who_is_also_an_owner_is_downgraded_they_will_also_be_downgraded_from_owner()
    {
        $owner1 = Employee::factory()->owner()->create();
        $owner2 = Employee::factory()->owner()->for($owner1->company)->create();
        $owner3 = Employee::factory()->owner()->for($owner1->company)->create();
        $this->actingAs($owner1->user, 'api');

        $response = $this->delete("/locations/$owner1->company_id/employees/$owner2->id/admin");
        $response->assertStatus(204);
        $employee = Employee::find($owner2->id);

        $this->assertFalse($employee->admin);
        $this->assertFalse($employee->owner);
    }

    /** @test */
    public function an_admin_who_is_the_sole_owner_cannot_be_downgraded()
    {
        $owner = Employee::factory()->owner()->create();
        $this->actingAs($owner->user, 'api');

        $response = $this->delete("/locations/$owner->company_id/employees/$owner->id/admin");

        $response->assertStatus(400);
    }
}