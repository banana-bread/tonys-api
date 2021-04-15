<?php

namespace Tests\Unit\Models\TimeSlot;

use App\Exceptions\InvalidParameterException;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\TimeSlot;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class IsAvailableDuringTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function it_accepts_a_single_time_slot()
    {
        $client = Client::factory()->create();
        $timeSlot = TimeSlot::factory()->create();

        $isAvailable = $client->isAvailableDuring($timeSlot);

        $this->assertTrue($isAvailable);
    }

    /** @test */
    public function it_accepts_a_collection_of_time_slots()
    {
        $client = Client::factory()->create();
        $timeSlots = TimeSlot::factory()->count(3)->create();

        $isAvailable = $client->isAvailableDuring($timeSlots);

        $this->assertTrue($isAvailable);
    }

    /** @test */
    public function it_throws_an_exception_if_param_is_not_a_collection_or_time_slot_model()
    {
        $client = Client::factory()->create();
        $notACollectionOrTimeSlot = 'timeslot';

        $this->expectException(InvalidParameterException::class);
        $client->isAvailableDuring($notACollectionOrTimeSlot);

    }

    /** @test */
    public function it_returns_false_if_a_booking_exists_starting_after_and_ending_before()
    {
        $employee = Employee::factory()->create();
        $booking = Booking::factory()->create([
            'employee_id' => $employee->id,
            'started_at' => today()->addHours(10)->addMinutes(30),
            'ended_at' => today()->addHours(11),
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addHours(10),
            'end_time' => today()->addHours(12),
        ]);

        $isAvailable = $booking->client->isAvailableDuring($timeSlot);

        $this->assertFalse($isAvailable);
    }

    /** @test */
    public function it_returns_false_if_a_booking_exists_starting_after_and_ending_after()
    {
        $employee = Employee::factory()->create();
        $booking = Booking::factory()->create([
            'employee_id' => $employee->id,
            'started_at' => today()->addHours(9)->addMinutes(30),
            'ended_at' => today()->addHours(10)->addMinutes(30),
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addHours(9),
            'end_time' => today()->addHours(10),
        ]);

        $isAvailable = $booking->client->isAvailableDuring($timeSlot);

        $this->assertFalse($isAvailable);
    }

    /** @test */
    public function it_returns_false_if_a_booking_exists_starting_before_and_ending_after()
    {
        $employee = Employee::factory()->create();
        $booking = Booking::factory()->create([
            'employee_id' => $employee->id,
            'started_at' => today()->addHours(8),
            'ended_at' => today()->addHours(11),
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addHours(9),
            'end_time' => today()->addHours(9)->addMinutes(30),
        ]);

        $isAvailable = $booking->client->isAvailableDuring($timeSlot);

        $this->assertFalse($isAvailable);
    }

    /** @test */
    public function it_returns_false_if_a_booking_exists_starting_before_and_ending_before()
    {
        $employee = Employee::factory()->create();
        $booking = Booking::factory()->create([
            'employee_id' => $employee->id,
            'started_at' => today()->addHours(8),
            'ended_at' => today()->addHours(9),
        ]);
        $timeSlot = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addHours(9),
            'end_time' => today()->addHours(9)->addMinutes(30),
        ]);

        $isAvailable = $booking->client->isAvailableDuring($timeSlot);

        $this->assertFalse($isAvailable);
    }

    /** @test */
    public function if_a_collection_of_time_slots_is_passed_the_first_start_time_and_last_end_time_will_be_used() 
    {
        $employee = Employee::factory()->create();

        $booking = Booking::factory()->create([
            'employee_id' => $employee->id,
            'started_at' => today()->addHours(10),
            'ended_at' => today()->addHours(10)->addMinutes(30),
        ]);
        $timeSlot1 = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addHours(9)->addMinutes(30),
            'end_time' => today()->addHours(10),
        ]);
        $timeSlot2 = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addHours(10),
            'end_time' => today()->addHours(10)->addMinutes(30),
        ]);
        $timeSlot3 = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addHours(10)->addMinutes(30),
            'end_time' => today()->addHours(11),
        ]);
        $timeSlots = new Collection();
        $timeSlots->push($timeSlot1);
        $timeSlots->push($timeSlot2);
        $timeSlots->push($timeSlot3);

        $isAvailable = $booking->client->isAvailableDuring($timeSlots);

        $this->assertFalse($isAvailable);
    }


}
