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
            'admin' => false,
            'owner' => false,
            'settings' => TestMock::employee_settings(),
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

        $response = $this->post('/employees', [ 
            'company_id' => Company::factory()->create()->id,
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

        $response = $this->post('/employees', [ 
            'company_id' => Company::factory()->create()->id,
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

        $response = $this->get("employees/$employee->id");

        $response->assertOk();
        $this->assertEquals($employee->id, $response->json('data.employee.id'));
    }
}
