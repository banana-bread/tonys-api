<?php

namespace Tests\Feature;

use App\Jobs\CreateEmployeeTimeSlots;
use App\Mail\CompanyCreated;
use App\Models\Company;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestMock;

class CompanyTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_company_can_be_created()
    {
        $response = $this->post('/companies', $this->company_data());

        $response->assertCreated();
    }

    /** @test */
    public function when_a_company_is_created_an_owner_is_also_created()
    {
        $response = $this->post('/companies', $this->company_data());

        $this->assertTrue($response->json('data.company.owner.owner'));
    }

    /** @test */
    public function creating_an_company_queues_a_job_to_send_an_email_confirmation_to_the_company_owner()
    {
        Mail::fake();

        $response = $this->post('/companies', $this->company_data()); 

        Mail::assertQueued(CompanyCreated::class, function ($job) use ($response) {
            return $job->to[0]['address'] == $response->json('data.company.owner.email');
        });
    }


    /** @test */
    public function creating_a_company_queues_a_job_to_create_time_slots_for_its_owner()
    {
        Bus::fake();

        $response = $this->post('/companies', $this->company_data()); 

        Bus::assertDispatched(function (CreateEmployeeTimeSlots $job) use ($response) {
            return $response->json('data.company.owner.id') === $job->employee->id;
        });
    }

    /** @test */
    public function a_company_can_be_retrieved()
    {
        $company = Company::factory()->create();

        $response = $this->get("/companies/$company->id");
        
        $response->assertOk();
        $this->assertEquals($company->id, $response->json('data.company.id'));
    }

    // HELPERS
    private function company_data(): array
    {
        return [ 
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'phone' => '+18195551234',
            'time_slot_duration' => $this->faker->numberBetween(1000, 2000),
            'booking_cancellation_period' => $this->faker->numberBetween(1000, 2000),
            'settings' => TestMock::company_settings(),
            'user' => [
                'name' => $this->faker->name,
                'email' => $this->faker->email,
                'phone' => '+18195551234',
                'password' => $this->faker->password,
            ]
        ];
    }
}
