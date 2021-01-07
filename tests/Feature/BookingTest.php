<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class BookingTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    // This is bad
    /** @test */
    // public function a_booking_can_be_created(): void
    // {
    //     $booking = Booking::factory()->raw();
        
    //     $response = $this->post("/bookings", $booking);

    //     $response->assertCreated();
    //     $this->assertDatabaseHas('bookings', [
    //         'id' => Arr::get($response->json(), 'data.id')
    //     ]);
    // }

    /** @test */
    // public function a_booking_can_be_created_before_being_reserved_by_a_client(): void
    // {
    //     $booking = Booking::factory()->raw(['client_id' => null]);

    //     $response = $this->post("/bookings", $booking);

    //     $response->assertStatus(201);
    //     $this->assertDatabaseHas('bookings', [
    //         'id' => Arr::get($response->json(), 'data.id')
    //     ]);
    // }

    /** @test */
    public function a_booking_requires_an_employee(): void
    {
        $booking = Booking::factory()->raw(['employee_id' => null]);

        $this->post("/bookings", $booking)
             ->assertStatus(400);
    }

    /** @test */
    public function a_booking_requires_a_started_at_date_time(): void
    {
        $booking = Booking::factory()->raw(['started_at' => null]);

        $this->post("/bookings", $booking)
             ->assertStatus(400);
    }

    /** @test */
    public function a_booking_requires_an_ended_at_date_time(): void
    {
        $booking = Booking::factory()->raw(['ended_at' => null]);

        $this->post("/bookings", $booking)
             ->assertStatus(400);
    }

    /** @test */
    public function a_booking_must_start_before_it_ends(): void
    {
        $booking = Booking::factory()->raw([
            'started_at' => Carbon::now()->addMinutes(30),
            'ended_at' => Carbon::now()
        ]);

        $this->post("/bookings", $booking)
             ->assertStatus(400);
    }

    /** @test */
    public function a_client_can_reserve_a_booking(): void
    {
        $booking = Booking::factory()->create(['client_id' => null]);
    
        $client = Client::factory()->create();

        $response = $this->put("/bookings/$booking->id/client", [
            'client_id' => $client->id
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'client_id' => $client->id
        ]);
    }

    /** @test */
    public function a_client_cannot_reserve_bookings_with_overlapping_times(): void
    {
        $bookingStart = Carbon::today()->addHours(9);
        $bookingEnd = $bookingStart->copy()->addMinutes(30);
        $client = Client::factory()->create();

        $booking1 = Booking::factory()->create([
            'client_id' => $client->id,
            'started_at' => $bookingStart,
            'ended_at' => $bookingEnd
        ]);

        $booking2 = Booking::factory()->create([
            'client_id' => null,
            'started_at' => $bookingStart,
            'ended_at' => $bookingEnd
        ]);

        $response = $this->put("/bookings/$booking2->id/client", [
            'client_id' => $client->id
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('bookings', [
            'id' => $booking2->id,
            'client_id' => $client->id
        ]);
    }

    /** @test */
    public function a_client_cannot_reserve_a_booking_that_has_already_been_reserved(): void
    {
        $booking = Booking::factory()->create();
        $client = Client::factory()->create();

        $response = $this->put("/bookings/$booking->id/client", [
            'client_id' => $client->id
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
            'client_id' => $client->id
        ]);
    }

      /** @test */
      public function a_client_cannot_reserve_a_booking_that_has_been_overridden(): void
      {
          $booking = Booking::factory()->overridden()->create();
          $client = Client::factory()->create();
  
          $response = $this->put("/bookings/$booking->id/client", [
              'client_id' => $client->id
          ]);
  
          $response->assertStatus(400);
          $this->assertDatabaseMissing('bookings', [
              'id' => $booking->id,
              'client_id' => $client->id
          ]);
      }

    /** @test */
    public function an_employee_can_override_their_own_bookings(): void
    {
        // $response = $this->put("/bookings/$booking->id", [] );
    }

    /** @test */
    public function an_employee_cannot_override_another_employees_bookings(): void
    {

    }

    /** @test */
    public function an_admin_can_override_other_employees_bookings(): void
    {

    }    

    /** @test */
    public function a_client_can_cancel_a_booking_before_cancellation_period(): void
    {

    }
         
    /** @test */
    public function a_client_cannot_cancel_a_booking_within_cancellation_period(): void
    {
        
    }   

    /** @test */
    public function a_client_cannot_cancel_a_booking_after_cancellation_period(): void
    {

    }    

    /** @test */
    public function a_client_cannot_reserve_bookings_in_the_past(): void
    {

    }    

    /** @test */
    public function a_client_cannot_reserve_bookings_n_number_of_days_in_the_future(): void
    {

    }    

    /** @test */
    public function when_a_client_books_many_services_and_their_times_exceed_a_single_booking_the_next_booking_is_also_reserved_if_available(): void
    {

    }

    /** @test */
    public function a_client_cannot_book_many_services_if_their_times_exceed_a_single_booking_and_the_next_booking_is_not_available(): void
    {

    }
}
