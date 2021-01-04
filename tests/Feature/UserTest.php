<?php

namespace Tests\Feature;

use App\Exceptions\EmployeeAuthorizationException;
use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class UserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_client_can_create_an_account(): void
    {
        $mock = new UserMock();
        $attributes = $mock->a_request_to_create_a_client_account();

        $response = $this->post('/clients', $attributes);
         
        $response->assertCreated();
        $this->assertDatabaseHas('users', [ 'email' => $attributes['email'] ])
             ->assertDatabaseHas('clients', [ 'user_id' => Arr::get($response, 'data.user_id') ]);

    }

    /** @test */
    public function an_employee_can_create_an_account(): void
    {
        $mock = new UserMock();
        $attributes = $mock->a_request_to_create_an_employee_account();

        $response = $this->post('/employees', $attributes);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [ 'email' => $attributes['email'] ])
             ->assertDatabaseHas('employees', [ 'user_id' => Arr::get($response, 'data.user_id') ])
             ->assertFalse(Arr::get($response, 'data.admin'));
    }

    /** @test */
    public function an_employee_can_create_an_admin_account(): void
    {
        $mock = new UserMock();
        $attributes = $mock->a_request_to_create_an_employee_admin_account();

        $response = $this->post('/employees', $attributes);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [ 'email' => $attributes['email'] ])
             ->assertDatabaseHas('employees', [ 'user_id' => Arr::get($response, 'data.user_id') ])
             ->assertTrue(Arr::get($response, 'data.admin'));
    }

    /** @test */
    public function a_client_account_can_be_retrieved()
    {
        
    }

    /** @test */
    public function an_employee_account_can_be_retrieved()
    {

    }

    /** @test */
    public function an_admin_can_grant_employees_admin_privileges(): void
    {
        $admin = Employee::factory()->admin()->create();
        $employee = Employee::factory()->create();
        $this->actingAs($admin->user);

        $response = $this->put("/employees/$employee->id", ['admin' => true]);

        $response->assertOk();
        $this->assertDatabaseHas('employees', [
            'user_id' => $employee->user->id,
            'admin' => true
        ]);
    }
    
    // TODO: should add 'owner' to the list of account types.  Not sure if it needs to be part of the MVP yet.
    /** @test */
    public function an_admin_can_revoke_employees_admin_privileges(): void
    {
        $admin1 = Employee::factory()->admin()->create();
        $admin2 = Employee::factory()->admin()->create();
        $this->actingAs($admin1->user);

        $response = $this->put("/employees/$admin2->id", ['admin' => false]);

        $response->assertOk();
        $this->assertDatabaseHas('employees', [
            'user_id' => $admin2->user->id,
            'admin' => false
        ]);
    }

    /** @test */
    public function an_employee_cannot_grant_admin_privileges(): void
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();
        $this->actingAs($employee1->user);
        
        $response = $this->put("/employees/$employee2->id", ['admin' => true]);
        
        $response->assertStatus(400);
        $this->assertDatabaseHas('employees', [
            'user_id' => $employee2->user->id,
            'admin' => false
        ]);
    }

    /** @test */
    public function an_employee_cannot_revoke_admin_privileges(): void
    {
        $employee = Employee::factory()->create();
        $admin = Employee::factory()->admin()->create();
        $this->actingAs($employee->user);
        
        $response = $this->put("/employees/$admin->id", ['admin' => false]);
        
        $response->assertStatus(400);
        $this->assertDatabaseHas('employees', [
            'user_id' => $admin->user->id,
            'admin' => true
        ]);
    }


}
