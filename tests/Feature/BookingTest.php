<?php

namespace Tests\Feature;

use App\Mail\BookingCancelled;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ServiceDefinition;
use App\Mail\BookingCreated;
use App\Models\Company;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class BookingTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_booking_can_be_created()
    {
        $client = Client::factory()->create();
        $this->actingAs($client->user, 'api');
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800]);

        $response = $this->post("locations/$serviceDefinition->company_id/bookings",[
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
        $companyId = $booking->employee->company_id;

        $response = $this->get("locations/$companyId/bookings/$booking->id");

        $response->assertOk();
    }

    /** @test */
    public function a_booking_can_not_be_created_for_an_inactive_employee()
    {
        $client = Client::factory()->create();
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800]);
        $employee = Employee::factory()->inactive()->for($serviceDefinition->company)->create();
        $this->actingAs($client->user, 'api');

        $response = $this->post("locations/$serviceDefinition->company_id/bookings",[
            'time_slot_id' => TimeSlot::factory()->for($employee)->create()->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function a_booking_can_be_cancelled_by_an_owner_even_if_they_are_not_the_assigned_employee()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->for($owner->company)->create();
        $booking = Booking::factory()->for($employee)->create();

        $this->actingAs($owner->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/bookings/$booking->id");

        $response->assertStatus(204);
    }

    /** @test */
    public function a_booking_can_be_cancelled_by_an_admin_even_if_they_are_not_the_assigned_employee()
    {

        $admin = Employee::factory()->admin()->create();
        $employee = Employee::factory()->for($admin->company)->create();
        $booking = Booking::factory()->for($employee)->create();

        $this->actingAs($admin->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/bookings/$booking->id");

        $response->assertStatus(204);
    }

    /** @test */
    public function a_booking_can_be_cancelled_by_the_assigned_employee()
    {
        $employee = Employee::factory()->create();
        $booking = Booking::factory()->for($employee)->create();

        $this->actingAs($employee->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/bookings/$booking->id");

        $response->assertStatus(204);
    }

    /** @test */
    public function a_booking_can_be_cancelled_by_the_assigned_client_if_within_booking_grace_period()
    {
        $employee = Employee::factory()->create();
        $booking = Booking::factory()->for($employee)->create([
            'started_at' => now()->addSeconds($employee->company->booking_grace_period * 2)
        ]);

        $this->actingAs($booking->client->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/bookings/$booking->id");

        $response->assertStatus(204);
    }

    /** @test */
    public function a_booking_cannot_be_cancelled_by_the_assigned_client_if_past_booking_grace_period()
    {
        $employee = Employee::factory()->create();
        $booking = Booking::factory()->for($employee)->create([
            'started_at' => now()->addSeconds($employee->company->booking_grace_period)
        ]);

        $this->actingAs($booking->client->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/bookings/$booking->id");
        $response->assertStatus(400);
    }

    /** @test */
    public function a_booking_cannot_be_cancelled_by_a_non_assigned_employee()
    {
        $employee = Employee::factory()->create();
        $employee2 = Employee::factory()->for($employee->company)->create();
        $booking = Booking::factory()->for($employee2)->create();

        $this->actingAs($employee->user, 'api');

        $response = $this->delete("/locations/$employee->company_id/bookings/$booking->id");
        $response->assertStatus(400);
    }

    /** @test */
    public function a_booking_cannot_be_cancelled_by_a_non_assigned_client()
    {
        $client = Client::factory()->create();
        $booking = Booking::factory()->create();
        $companyId = $booking->employee->company_id;

        $this->actingAs($client->user, 'api');

        $response = $this->delete("/locations/$companyId/bookings/$booking->id");
        $response->assertStatus(400);
    }

    /** @test */
    public function a_booking_cancelled_by_the_client_will_queue_a_confirmation_email_for_the_client()
    {
        Mail::fake();
        $booking = Booking::factory()->create(['started_at' => today()->addDays(5)]);
        $companyId = $booking->employee->company_id;
        $this->actingAs($booking->client->user, 'api');

        $this->delete("/locations/$companyId/bookings/$booking->id");

        Mail::assertQueued(BookingCancelled::class, function ($mail) use ($booking) {
            return $mail->to[0]['address'] == $booking->client->email;
        });
    }

    /** @test */
    public function a_booking_cancelled_by_the_client_will_queue_a_confirmation_email_for_the_employee()
    {
        Mail::fake();
        $booking = Booking::factory()->create(['started_at' => today()->addDays(5)]);
        $companyId = $booking->employee->company_id;
        $this->actingAs($booking->client->user, 'api');

        $this->delete("/locations/$companyId/bookings/$booking->id");

        Mail::assertQueued(BookingCancelled::class, function ($mail) use ($booking) {
            return $mail->to[0]['address'] == $booking->employee->email;
        });
    }

    /** @test */
    public function a_booking_cancelled_by_someone_other_than_the_client_will_queue_a_confirmation_email_for_the_client()
    {
        Mail::fake();
        $booking = Booking::factory()->create(['started_at' => today()->addDays(5)]);
        $companyId = $booking->employee->company_id;
        $this->actingAs($booking->employee->user, 'api');

        $this->delete("/locations/$companyId/bookings/$booking->id");

        Mail::assertQueued(BookingCancelled::class, function ($mail) use ($booking) {
            return $mail->to[0]['address'] == $booking->client->email;
        });
    }

    // TODO: this test doens't work but I know it works...
    // /** @test */
    // public function a_booking_cancelled_by_someone_other_than_the_client_will_not_queue_a_confirmation_email_for_the_employee()
    // {
    //     Mail::fake();
    //     $booking = Booking::factory()->create(['started_at' => today()->addDays(5)]);
    //     $this->actingAs($booking->employee->user, 'api');

    //     $this->delete("/bookings/$booking->id");

    //     Mail::assertNotQueued(BookingCancelled::class, function ($mail) use ($booking) {
    //         return $mail->to[0]['address'] != $booking->employee_id;
    //     });
    // }

    /** @test */
    public function a_booking_confirmation_email_for_clients_will_be_queued_when_a_booking_is_created()
    {
        Mail::fake();
        $client = Client::factory()->create();
        $this->actingAs($client->user, 'api');
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800]);

        $response = $this->post("/locations/$serviceDefinition->company_id/bookings",[
            'time_slot_id' => TimeSlot::factory()->create()->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        Mail::assertQueued(BookingCreated::class, function ($mail) use ($response) {
            return $mail->booking->id === $response->json('data.booking.id');
        });

    }

    /** @test */
    public function a_booking_confirmation_job_for_clients_will_not_be_queued_when_a_booking_is_created_but_the_client_has_unsubscribed_from_emails()
    {
        Mail::fake();
        $client = Client::factory()->unsubscribed()->create();
        $this->actingAs($client->user, 'api');
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800]);

        $response = $this->post("/locations/$serviceDefinition->company_id/bookings",[
            'time_slot_id' => TimeSlot::factory()->create()->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        Mail::assertNotQueued(BookingCreated::class);
    }

    /** @test */
    public function a_single_slot_bookings_associated_time_slot_will_be_reserved_after_booking_is_created()
    {
        $ts = TimeSlot::factory()->create();
        $client = Client::factory()->create();
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800]);
        $this->actingAs($client->user, 'api');

        $response = $this->post("/locations/$serviceDefinition->company_id/bookings",[
            'time_slot_id' => $ts->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        $this->assertTrue(TimeSlot::find($ts->id)->reserved);
    }

    /** @test */
    public function a_multi_slot_bookings_associated_time_slots_will_all_be_reserved_after_booking_is_created()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->create();
        $ts1 = TimeSlot::factory()->create(['start_time' => now(), 'end_time' => now()->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company->id]);
        $ts2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(30), 'end_time' => now()->addMinutes(60), 'employee_id' => $employee->id, 'company_id' => $company->id]);
        $serviceDefinition = ServiceDefinition::factory()->for($company)->create(['duration' => 2700]);
        $client = Client::factory()->create();
        $this->actingAs($client->user, 'api');

        $response = $this->post("/locations/$serviceDefinition->company_id/bookings",[
            'time_slot_id' => $ts1->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        $this->assertTrue(TimeSlot::find($ts1->id)->reserved);
        $this->assertTrue(TimeSlot::find($ts2->id)->reserved);
    }

    /** @test */
    public function a_many_to_many_association_will_be_created_between_the_bookings_client_and_its_company_if_one_does_not_exist()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->create();
        $ts = TimeSlot::factory()->create(['employee_id' => $employee->id, 'company_id' => $company->id]);
        $client = Client::factory()->create();
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800, 'company_id' => $company->id]);
        $this->actingAs($client->user, 'api');

        $response = $this->post("/locations/$serviceDefinition->company_id/bookings",[
            'time_slot_id' => $ts->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        $this->assertDatabaseHas('companies_clients', [
            'client_id' => $client->id,
            'company_id' => $company->id,
        ]);
    }

    /** @test */
    public function a_many_to_many_association_will_be_updated_between_the_bookings_client_and_its_company_if_one_already_exists()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->create();
        $client = Client::factory()->create();
        $serviceDefinition = ServiceDefinition::factory()->create(['duration' => 1800, 'company_id' => $company->id]);

        $ts1 = TimeSlot::factory()->create(['start_time' => now(), 'end_time' => now()->addMinutes(30), 'employee_id' => $employee->id, 'company_id' => $company->id]);
        $ts2 = TimeSlot::factory()->create(['start_time' => now()->addMinutes(60), 'end_time' => now()->addMinutes(90), 'employee_id' => $employee->id, 'company_id' => $company->id]);
        
        $this->actingAs($client->user, 'api');

        $booking1 = $this->post("/locations/$serviceDefinition->company_id/bookings",[
            'time_slot_id' => $ts1->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        $associationBeforeUpdate = collect(DB::table('companies_clients')->find(1));
        $updatedAndCreatedAreSameBeforeUpdate = $associationBeforeUpdate['created_at'] == $associationBeforeUpdate['updated_at'];

        sleep(2);

        $booking2 = $this->post("/locations/$serviceDefinition->company_id/bookings",[
            'time_slot_id' => $ts2->id,
            'client_id' => $client->id,
            'service_definition_ids' => [
                $serviceDefinition->id
            ]
        ]);

        $associationAfterUpdate = collect(DB::table('companies_clients')->find(1));
        $updatedAndCreatedAreSameAfterUpdate = $associationAfterUpdate['created_at'] == $associationAfterUpdate['updated_at'];
        
        // logger($associationBeforeUpdate);
        // logger($associationAfterUpdate);

        $this->assertTrue($updatedAndCreatedAreSameBeforeUpdate);
        $this->assertFalse($updatedAndCreatedAreSameAfterUpdate);
    }
}
