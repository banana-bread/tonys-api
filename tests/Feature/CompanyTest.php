<?php

namespace Tests\Feature;

use App\Models\Company;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_company_can_be_created()
    {
        $response = $this->post('/companies', [ 
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'phone' => '+18195551234',
            'time_slot_duration' => $this->faker->numberBetween(1000, 2000),
            'booking_cancellation_period' => $this->faker->numberBetween(1000, 2000),
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function a_company_can_be_retrieved()
    {
        $company = Company::factory()->create();

        $response = $this->get("/companies/$company->id");
        
        $response->assertOk();
        $this->assertEquals($company->id, $response->json('data.company.id'));
    }
}
