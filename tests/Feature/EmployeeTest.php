<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_can_create_an_account()
    {
        $response = $this->post('/employees', [ 
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
            'admin' => false
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function an_employee_can_create_an_admin_account()
    {
        $response = $this->post('/employees', [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
            'admin' => true
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function an_employee_account_can_be_retrieved()
    {
        $employee = Employee::factory()->create(); 
        $this->actingAs($employee->user, 'api');

        $response = $this->get("employees/$employee->id");

        $response->assertOk();
        $this->assertEquals($employee->id, $response->json('data.employee.id'));
    }
}
