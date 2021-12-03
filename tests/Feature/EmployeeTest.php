<?php

namespace Tests\Feature;

use App\Jobs\CreateEmployeeTimeSlots;
use App\Mail\EmployeeRegistered;
use App\Models\Company;
use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestMock;

// TODO: these tests are broken because not sure how to fake signed urls for testing
class EmployeeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_can_create_an_account()
    {
        $company = Company::factory()->create();
        $response = $this->post("/locations/$company->id/employees", [ 
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
            'admin' => false,
            'owner' => false,
            'settings' => TestMock::employee_settings(),
        ]); 

        $response->assertCreated();
    }

    /** @test */
    public function an_employee_can_create_an_admin_account()
    {
        $company = Company::factory()->create();
        $response = $this->post("/locations/$company->id/employees", [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
            'admin' => true,
            'owner' => false,
            'settings' => TestMock::employee_settings(),
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function creating_an_employee_account_queues_a_job_to_send_an_email_confirmation()
    {
        Mail::fake();
        $company = Company::factory()->create();

        $response = $this->post("/locations/$company->id/employees", [ 
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
            'admin' => false,
            'owner' => false,
            'settings' => TestMock::employee_settings(),
        ]); 

        Mail::assertQueued(EmployeeRegistered::class, function ($job) use ($response) {
            return $job->to[0]['address'] == $response->json('data.employee.email');
        });
    }

    /** @test */
    public function creating_an_employee_account_queues_a_job_to_create_time_slots()
    {
        Bus::fake();
        $company = Company::factory()->create();

        $response = $this->post("/locations/$company->id/employees", [ 
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
            'admin' => false,
            'owner' => false,
            'settings' => TestMock::employee_settings(),
        ]); 

        Bus::assertDispatched(function (CreateEmployeeTimeSlots $job) use ($response) {
            return $response->json('data.employee.id') === $job->employee->id;
        });
    }

    /** @test */
    public function an_employee_account_can_be_retrieved()
    {
        $employee = Employee::factory()->create(); 
        $this->actingAs($employee->user, 'api');

        $response = $this->get("/locations/$employee->company_id/employees/$employee->id");

        $response->assertOk();
        $this->assertEquals($employee->id, $response->json('data.employee.id'));
    }

    /** @test */
    public function all_company_employees_can_be_retrieved()
    {
        $company = Company::factory()->create();
        $employees = Employee::factory()->for($company)->count(5)->create(); 
        $this->actingAs($employees->first()->user, 'api');
        
        $response = $this->get("/locations/$company->id/company/employees");

        $response->assertOk();
        $this->assertCount(5, $response->json('data.employees'));
    }

    /** @test */
    public function all_company_employees_retrieved_will_be_scoped_to_the_same_company()
    {
        $company = Company::factory()->create();
        $employees = Employee::factory()->for($company)->count(5)->create(); 
        Employee::factory()->create();
        $this->actingAs($employees->first()->user, 'api');

        $response = $this->get("/locations/$company->id/company/employees");

        $response->assertOk();
        $this->assertCount(5, $response->json('data.employees'));
    }

    /** @test */
    public function all_booking_employees_retrieved_will_be_scoped_to_the_same_company()
    {
        $company = Company::factory()->create();
        $employees = Employee::factory()->for($company)->count(5)->create(); 
        Employee::factory()->create();

        $response = $this->get("/locations/$company->id/booking/employees");

        $response->assertOk();
        $this->assertCount(5, $response->json('data.employees'));
    }

    // TODO: should we sepeate employee indexes? For management app we want to grab ALL employees, for booking only active ones.
    /** @test */
    public function all_booking_employees_retrieved_will_be_active()
    {
        $company = Company::factory()->create();
        $activeEmployees = Employee::factory()->for($company)->count(5)->create(); 
        $inactiveEmployee = Employee::factory()->for($company)->inactive()->create();

        $response = $this->get("/locations/$company->id/booking/employees");

        $response->assertOk();

        $hasAnInactive = collect($response->json('data.employees'))->contains(function($employee) {
            return !$employee['bookings_enabled'];
        });
        $this->assertFalse($hasAnInactive);
    }



    /** @test */
    // public function an_employee_can_update_their_account()
    // {
    //     $employee = Employee::factory()->create();
    //     $this->actingAs($employee->user, 'api');

    //     $response = $this->put("/locations/$employee->company_id/employees/$employee->id", 
    //         $employee
    //     );

    //     $response->assertOk();
    // }
}
