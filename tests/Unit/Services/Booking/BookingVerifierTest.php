<?php

namespace Tests\Feature\Services\Booking;

use App\Exceptions\BookingException;
use App\Models\ServiceDefinition;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\TimeSlot;
use App\Services\Booking\BookingVerifier;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingVerifierTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_client_cannot_create_bookings_starting_after_and_ending_before_existing_bookings(): void
    {
        $client = Client::factory()->create();
        $employee = Employee::factory()->create();
        $booking1 = Booking::factory()->create([
            'client_id' => $client->id,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(9),
            'ended_at' => Carbon::today()->addHours(10)->addMinutes(30)
        ]);
        $booking2 = Booking::factory()->raw([
            'client_id' => null,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(9)->addMinutes(30),
            'ended_at' => Carbon::today()->addHours(10)
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => $booking2['started_at'],
            'end_time' => $booking2['ended_at'],
        ]);
        $verifier = new BookingVerifier($timeSlot);

        $this->expectException(BookingException::class);
        $verifier->verifyNoOverlap($client);
    }

    /** @test */
    public function a_client_cannot_create_bookings_starting_after_and_ending_after_existing_bookings(): void
    {
        $client = Client::factory()->create();
        $employee = Employee::factory()->create();
        $booking1 = Booking::factory()->create([
            'client_id' => $client->id,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(9),
            'ended_at' => Carbon::today()->addHours(10)
        ]);
        $booking2 = Booking::factory()->raw([
            'client_id' => null,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(9)->addMinutes(30),
            'ended_at' => Carbon::today()->addHours(10)->addMinutes(30)
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => $booking2['started_at'],
            'end_time' => $booking2['ended_at'],
        ]);
        $verifier = new BookingVerifier($timeSlot);

        $this->expectException(BookingException::class);
        $verifier->verifyNoOverlap($client);
    }

    /** @test */
    public function a_client_cannot_create_bookings_starting_before_and_ending_after_existing_bookings(): void
    {
        $client = Client::factory()->create();
        $employee = Employee::factory()->create();
        $booking1 = Booking::factory()->create([
            'client_id' => $client->id,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(9),
            'ended_at' => Carbon::today()->addHours(9)->addMinutes(30)
        ]);
        $booking2 = Booking::factory()->raw([
            'client_id' => null,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(8)->addMinutes(30),
            'ended_at' => Carbon::today()->addHours(10)
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => $booking2['started_at'],
            'end_time' => $booking2['ended_at'],
        ]);
        $verifier = new BookingVerifier($timeSlot);

        $this->expectException(BookingException::class);
        $verifier->verifyNoOverlap($client);
    }

    /** @test */
    public function a_client_cannot_create_bookings_starting_before_and_ending_before_existing_bookings(): void
    {
        $client = Client::factory()->create();
        $employee = Employee::factory()->create();
        $booking1 = Booking::factory()->create([
            'client_id' => $client->id,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(9),
            'ended_at' => Carbon::today()->addHours(10)
        ]);
        $booking2 = Booking::factory()->raw([
            'client_id' => null,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(8)->addMinutes(30),
            'ended_at' => Carbon::today()->addHours(9)
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => $booking2['started_at'],
            'end_time' => $booking2['ended_at'],
        ]);
        $verifier = new BookingVerifier($timeSlot);

        $this->expectException(BookingException::class);
        $verifier->verifyNoOverlap($client);
    }

    /** @test */
    public function overlapping_bookings_only_count_if_they_belong_to_the_same_client(): void
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $employee = Employee::factory()->create();
        $booking1 = Booking::factory()->create([
            'client_id' => $client1->id,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(9),
            'ended_at' => Carbon::today()->addHours(10)
        ]);
        $booking2 = Booking::factory()->raw([
            'client_id' => null,
            'employee_id' => $employee->id,
            'started_at' => Carbon::today()->addHours(8)->addMinutes(30),
            'ended_at' => Carbon::today()->addHours(9)
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => $booking2['started_at'],
            'end_time' => $booking2['ended_at'],
        ]);
        $verifier = new BookingVerifier($timeSlot);

        $response = $verifier->verifyNoOverlap($client2);
        $this->assertNull($response);
    }

    /** @test */
    public function a_client_cannot_create_a_booking_on_a_reserved_time_slot()
    {
        $timeSlot = TimeSlot::factory()->reserved()->create();
        $verifier = new BookingVerifier($timeSlot);

        $this->expectException(BookingException::class);
        $verifier->verifyStillAvailable();
    }

    /** @test */
    public function a_client_cannot_create_a_multi_slot_booking_when_successive_time_slots_are_reserved()
    {
        $employee = Employee::factory()->create();
        $timeSlot1 = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => Carbon::today()->addHours(9),
            'end_time' => Carbon::today()->addHours(9)->addMinutes(30)
        ]);
        $timeSlot2 = TimeSlot::factory()->reserved()->create([
            'employee_id' => $employee->id,
            'start_time' => Carbon::today()->addHours(9)->addMinutes(30),
            'end_time' => Carbon::today()->addHours(10)
        ]);
        $slotsRequired = 2;
        $verifier = new BookingVerifier($timeSlot1);
        $nextSlots = $timeSlot1->getNextSlots($slotsRequired);

        $this->expectException(BookingException::class);
        $verifier->verifyNextSlotsAreAvailable($nextSlots);
    }

    /** @test */
    public function a_client_cannot_create_a_multi_slot_booking_when_successive_time_slots_are_on_different_days()
    {
        $employee = Employee::factory()->create();
        $timeSlot1 = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => Carbon::today()->addHours(9),
            'end_time' => Carbon::today()->addHours(9)->addMinutes(30)
        ]);
        $timeSlot2 = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => Carbon::today()->addDay()->addHours(9)->addMinutes(30),
            'end_time' => Carbon::today()->addDay()->addHours(10)
        ]);
        $slotsRequired = 2;
        $verifier = new BookingVerifier($timeSlot1);
        $nextSlots = $timeSlot1->getNextSlots($slotsRequired);

        $this->expectException(BookingException::class);
        $verifier->verifyNextSlotsAreAvailable($nextSlots);
    }

    /** @test */
    public function a_client_cannot_create_a_multi_slot_booking_when_successive_time_slots_belong_to_different_employees()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();
        $timeSlot1 = TimeSlot::factory()->create([
            'employee_id' => $employee1->id,
            'start_time' => Carbon::today()->addHours(9),
            'end_time' => Carbon::today()->addHours(9)->addMinutes(30)
        ]);
        $timeSlot2 = TimeSlot::factory()->create([
            'employee_id' => $employee2->id,
            'start_time' => Carbon::today()->addHours(9)->addMinutes(30),
            'end_time' => Carbon::today()->addHours(10)
        ]);
        $slotsRequired = 2;
        $verifier = new BookingVerifier($timeSlot1);
        $nextSlots = $timeSlot1->getNextSlots($slotsRequired);

        $this->expectException(BookingException::class);
        $verifier->verifyNextSlotsAreAvailable($nextSlots);
    }
}