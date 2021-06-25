<?php

namespace Tests\Feature;

use App\Mail\EmployeeInvitationSent;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class EmployeeInvitationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_can_receive_an_invitation()
    {
        Mail::fake();
        $company = Company::factory()->create();
        $owner = Employee::factory()->for($company)->owner()->create();
        $this->actingAs($owner->user, 'api');

        $this->post("/locations/$company->id/employees/invitation", [
            'emails' => [$this->faker->email]
        ]);

        Mail::assertQueued(EmployeeInvitationSent::class);
    }

    /** @test */
    public function an_employee_cannot_send_an_invitation()
    {
        Mail::fake();
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->create();
        $this->actingAs($employee->user, 'api');

        $request = $this->post("/locations/$company->id/employees/invitation", [
            'emails' => [$this->faker->email]
        ]);

        $request->assertStatus(403);
        Mail::assertNotQueued(EmployeeInvitationSent::class);
    }

    /** @test */
    public function an_admin_cannot_send_an_invitation()
    {
        Mail::fake();
        $company = Company::factory()->create();
        $admin = Employee::factory()->for($company)->admin()->create();
        $this->actingAs($admin->user, 'api');

        $request = $this->post("/locations/$company->id/employees/invitation", [
            'emails' => [$this->faker->email]
        ]);

        $request->assertStatus(403);
        Mail::assertNotQueued(EmployeeInvitationSent::class);
    }

    /** @test */
    public function an_owner_can_send_inviations_to_more_than_one_employee_at_a_time()
    {
        
    }
}

