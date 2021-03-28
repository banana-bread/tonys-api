<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class AuthedClientsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_authed_client_account_can_be_retrieved(): void
    {
        $client = Client::factory()->create();
        $this->actingAs($client->user, 'api');

        $response = $this->get("authed-client");
        $authedClientId = $response->json('data.authed-client.id');

        $response->assertOk();
        $this->assertEquals($client->id, $authedClientId);
    }
}
