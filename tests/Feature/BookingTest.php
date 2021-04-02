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


class BookingTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_booking_can_be_created()
    {
        $client = Client::factory()->create();
        $this->actingAs($client->user, 'api');
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800]);

        $response = $this->post('/bookings',[
            'time_slot_id' => TimeSlot::factory()->create()->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function a_booking_can_be_retrieved()
    {
        $booking = Booking::factory()->create();
        $this->actingAs($booking->client->user, 'api');

        $response = $this->get("/bookings/$booking->id");

        $response->assertOk();
    }

    /** @test */
    public function a_booking_can_be_cancelled()
    {
        $booking = Booking::factory()->create();
        $this->actingAs($booking->client->user, 'api');

        $response = $this->delete("/bookings/$booking->id");

        $response->assertStatus(204);
    }
}

