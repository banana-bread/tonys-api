<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ServiceDefinition;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceDefinitionTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_service_definition_can_be_created()
    {
        $owner = Employee::factory()->owner()->create();
        $this->actingAs($owner->user, 'api');

        $response = $this->post("/locations/$owner->company_id/service-definitions", [
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(1000, 5000),
            'duration' => $this->faker->numberBetween(1000, 5000)
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function a_service_definition_cannot_be_created_by_a_non_admin_employee()
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user, 'api');

        $response = $this->post("/locations/$employee->company_id/service-definitions", [
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(1000, 5000),
            'duration' => $this->faker->numberBetween(1000, 5000)
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function a_service_definition_cannot_be_created_by_a_client()
    {
        $client = Client::factory()->create();
        $company = Company::factory()->create();
        $this->actingAs($client->user, 'api');

        $response = $this->post("/locations/$company->id/service-definitions", [
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(1000, 5000),
            'duration' => $this->faker->numberBetween(1000, 5000)
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function a_service_definition_can_be_retrieved()
    {
        $service = ServiceDefinition::factory()->create();

        $response = $this->get("/locations/$service->company_id/service-definitions/$service->id");

        $response->assertOk();
        $this->assertEquals($service->id, $response->json('data.service_definition.id'));
    }

    /** @test */
    public function all_service_definitions_can_be_retrieved()
    {
        $company = Company::factory()->create();
        $services = ServiceDefinition::factory()->count(5)->for($company)->create();

        $response = $this->get("/locations/$company->id/service-definitions");

        $response->assertOk();
        $this->assertCount(5, $response->json('data.service_definitions'));
    }

    /** @test */
    public function all_service_definitions_retrieved_will_be_scoped_to_one_company()
    {
        $company = Company::factory()->create();
        $services = ServiceDefinition::factory()->count(5)->for($company)->create();
        $serviceForOtherCompany = ServiceDefinition::factory()->create();

        $companyId = $services->first()->company_id;
        $response = $this->get("/locations/$company->id/service-definitions");

        $response->assertOk();
        $this->assertCount(5, $response->json('data.service_definitions'));
    }

    /** @test */
    public function a_service_definition_can_be_updated()
    {
        $owner = Employee::factory()->owner()->create();
        $service = ServiceDefinition::factory()->create([
            'company_id' => $owner->company_id
        ]);
        $this->actingAs($owner->user, 'api');

        $response = $this->put("/locations/$service->company_id/service-definitions/$service->id", $service->toArray());

        $response->assertOk();
    }

    /** @test */
    public function a_service_definition_cannot_be_updated_by_a_non_admin_employee()
    {
        $employee = Employee::factory()->create();
        $service = ServiceDefinition::factory()->create([
            'company_id' => $employee->company_id
        ]);
        $this->actingAs($employee->user, 'api');

        $response = $this->put("locations/$service->company_id/service-definitions/$service->id", $service->toArray());

        $response->assertStatus(403);
    }

    /** @test */
    public function a_service_definition_cannot_be_updated_by_an_owner_of_a_different_company()
    {
        $employee = Employee::factory()->owner()->create();
        $service = ServiceDefinition::factory()->create();
        $this->actingAs($employee->user, 'api');

        $response = $this->put("locations/$service->company_id/service-definitions/$service->id", $service->toArray());

        $response->assertStatus(403);
    }

    /** @test */
    public function a_service_definition_cannot_be_updated_by_a_client()
    {
        $client = Client::factory()->create();
        $service = ServiceDefinition::factory()->create();
        $this->actingAs($client->user, 'api');

        $response = $this->put("locations/$service->company_id/service-definitions/$service->id", $service->toArray());

        $response->assertStatus(403);
    }

    /** @test */
    public function a_service_definition_can_be_deleted()
    {
        $owner = Employee::factory()->owner()->create();
        $service = ServiceDefinition::factory()->create([
            'company_id' => $owner->company_id
        ]);
        $this->actingAs($owner->user, 'api');

        $response = $this->delete("locations/$service->company_id/service-definitions/$service->id");

        $response->assertStatus(204);
        $this->assertSoftDeleted('service_definitions', $service->toArray());
    }

    /** @test */
    public function a_service_definition_cannot_be_deleted_by_a_non_admin_employee()
    {
        $employee = Employee::factory()->create();
        $service = ServiceDefinition::factory()->create([
            'company_id' => $employee->company_id
        ]);
        $this->actingAs($employee->user, 'api');

        $response = $this->delete("locations/$service->company_id/service-definitions/$service->id");

        $response->assertStatus(403);
    }

    /** @test */
    public function a_service_definition_cannot_be_deleted_by_an_owner_of_a_different_company()
    {
        $employee = Employee::factory()->owner()->create();
        $service = ServiceDefinition::factory()->create();
        $this->actingAs($employee->user, 'api');

        $response = $this->put("locations/$service->company_id/service-definitions/$service->id", $service->toArray());

        $response->assertStatus(403);
    }

    /** @test */
    public function a_service_definition_cannot_be_deleted_by_a_client()
    {
        $client = Client::factory()->create();
        $service = ServiceDefinition::factory()->create();
        $this->actingAs($client->user, 'api');

        $response = $this->delete("locations/$service->company_id/service-definitions/$service->id");

        $response->assertStatus(403);
    }
}
