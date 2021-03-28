<?php

namespace Tests\Unit\Services\TimeSlot;

use App\Mail\BookingCreated;
use App\Models\Booking;
use App\Models\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class SendEmailTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_booking_confirmation_sent_to_a_client_will_be_queued()
    {
        Mail::fake();
        $booking = Booking::factory()
            ->for(Client::factory())
            ->create();
    
        $booking->notify($booking->client);

        Mail::assertQueued(BookingCreated::class);
        // Mail::shouldReceive('send')-with(
        //     'email.booking.created',
        //     ['client' => $client]
        // );
    }

    /** @test */
    public function an_email_will_not_be_sent_to_susubscribed_users()
    {
        // TODO: implement!
    }


}
