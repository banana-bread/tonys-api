<?php

namespace Tests\Feature;

use App\Models\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AuthedClientTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_authed_client_account_can_be_retrieved(): void
    {
        $client = Client::factory()->create();
        $this->actingAs($client->user, 'api');

        $response = $this->get("client/authed");

        $response->assertOk();
        $this->assertEquals($client->id, $response->json('data.client.id'));
    }
}
