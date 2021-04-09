<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ServiceDefinition;
use App\Mail\BookingCreated;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


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

    // TODO: implement
    // /** @test */
    // public function a_booking_cannot_be_cancelled_if_the_time_to_start_time_is_less_than_the_cancellation_grace_period()
    // {
    //     $booking = Booking::factory()->create();
    //     $this->actingAs($booking->client->user, 'api');

    //     $response = $this->delete("/bookings/$booking->id");

    //     $response->assertStatus(204);
    // }

    /** @test */
    public function a_booking_confirmation_job_for_clients_will_be_queued_when_a_booking_is_created()
    {
        Mail::fake();
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

        Mail::assertQueued(BookingCreated::class, function ($job) use ($response) {
            return $job->booking->id === $response->json('data.booking.id');
        });
    }

    /** @test */
    public function a_booking_confirmation_job_for_clients_will_not_be_queued_when_a_booking_is_created_but_the_client_has_unsubscribed_from_emails()
    {
        Mail::fake();
        $client = Client::factory()->unsubscribed()->create();
        $this->actingAs($client->user, 'api');
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800]);

        $response = $this->post('/bookings',[
            'time_slot_id' => TimeSlot::factory()->create()->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        Mail::assertNotQueued(BookingCreated::class);
    }
}

