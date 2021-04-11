<?php

namespace Tests\Feature;

use App\Models\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_client_can_create_an_account()
    {
        $response = $this->post('/clients', [ 
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function a_created_client_account_is_subscribed_to_emails_by_default()
    {
        $response = $this->post('/clients', [ 
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
        ]);

        $client = Client::findOrFail($response->json('data.client.id'));
        
        $this->assertTrue($client->subscribes_to_emails);
    }

    /** @test */
    public function a_client_account_can_be_retrieved()
    {
        $client = Client::factory()->create();
        $this->actingAs($client->user, 'api');
        
        $response = $this->get("/clients/$client->id");

        $response->assertOk();
        $this->assertEquals($client->id, $response->json('data.client.id'));
    }
}
