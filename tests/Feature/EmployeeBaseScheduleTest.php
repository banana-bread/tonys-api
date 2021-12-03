<?php

namespace Tests\Feature;

use App\Jobs\UpdateEmployeeTimeSlots;
use App\Models\Company;
use App\Models\Employee;
use App\Models\TimeSlot;
use Facade\FlareClient\Time\Time;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestUtils;
use Illuminate\Support\Facades\Bus;

class EmployeeBaseScheduleTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_can_update_their_base_schedule()
    {
        $employee = Employee::factory()->create(['settings' => [
            'base_schedule' => TestUtils::mockBaseSchedule(9, 17)->toArray()
        ]]);
        $ts = TimeSlot::factory()->for($employee)->create();
        $this->actingAs($employee->user, 'api');
        $newSchedule = TestUtils::mockBaseSchedule(10, 17);

        $response = $this->put("locations/$employee->company_id/employees/$employee->id/base-schedule", [
            'base_schedule' => $newSchedule->toArray()
        ]);

        $response->assertOk();
        $this->assertTrue(Employee::findOrFail($employee->id)->base_schedule->matches($newSchedule));
    }

    /** @test */
    public function an_employee_cannot_update_other_employees_base_schedules()
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->for($employee1->company)->create();
        $ts = TimeSlot::factory()->for($employee1)->create();
        $this->actingAs($employee1->user, 'api');

        $response = $this->put("locations/$employee1->company_id/employees/$employee2->id/base-schedule", [
            'base_schedule' => TestUtils::mockBaseSchedule(9, 17)->toArray()
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function an_owner_can_update_other_employees_base_schedules()
    {
        $owner = Employee::factory()->owner()->create();
        $employee = Employee::factory()->for($owner->company)->create();
        $ts = TimeSlot::factory()->for($employee)->create();
        $this->actingAs($owner->user, 'api');

        $response = $this->put("locations/$owner->company_id/employees/$employee->id/base-schedule", [
            'base_schedule' => TestUtils::mockBaseSchedule(10, 17)->toArray()
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_update_other_employees_base_schedules()
    {
        $admin = Employee::factory()->admin()->create();
        $employee = Employee::factory()->for($admin->company)->create();
        $ts = TimeSlot::factory()->for($employee)->create();

        $this->actingAs($admin->user, 'api');

        $response = $this->put("locations/$admin->company_id/employees/$employee->id/base-schedule", [
            'base_schedule' => TestUtils::mockBaseSchedule(10, 17)->toArray()
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function reserved_slots_from_the_previous_schedule_will_stay_reserved_when_updated()
    {
        Bus::fake();
        $employee = Employee::factory()->create(['settings' => [
            'base_schedule' => TestUtils::mockBaseSchedule(9, 17)->toArray()
        ]]);
        $ts1 = TimeSlot::factory()->reserved()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addDay()->addHours(9),
            'end_time' => today()->addDay()->addHours(9)->addMinutes(30),
        ]);
        $ts2 = TimeSlot::factory()->create([
            'employee_id' => $employee->id,
            'start_time' => today()->addDay()->addHours(9)->addMinutes(30),
            'end_time' => today()->addDay()->addHours(10),
        ]);
        $this->actingAs($employee->user, 'api');


        $response = $this->put("locations/$employee->company_id/employees/$employee->id/base-schedule", [
            'base_schedule' => TestUtils::mockBaseSchedule(9, 14)->toArray()
        ]);

        $futureReservedSlots = Employee::findOrFail($employee->id)->time_slots()
            ->where('reserved', true)
            ->get();

        $this->assertCount(1, $futureReservedSlots);
    }

    /** @test */
    public function a_job_will_be_queued_when_base_schedule_is_updated()
    {
        Bus::fake();
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user, 'api');

        $response = $this->put("locations/$employee->company_id/employees/$employee->id/base-schedule", [
            'base_schedule' => TestUtils::mockBaseSchedule(10, 17)->toArray()
        ]);

        Bus::assertDispatched(function (UpdateEmployeeTimeSlots $job) use ($response) {
            return $response->json('data.employee.id') === $job->employee->id;
        });
    }

    // /** @test */
    // public function starting_time_cannot_be_before_company_starting_time()
    // {
    //     $company = Company::factory()->create(['settings' => [
    //         'base_schedule' => TestUtils::mockBaseSchedule(9, 17)->toArray()
    //     ]]);
    //     $employee = Employee::factory()->for($company)->create(['settings' => [
    //         'base_schedule' => TestUtils::mockBaseSchedule(9, 17)->toArray()
    //     ]]);
    //     $this->actingAs($employee->user, 'api');

    //     $response = $this->put("locations/$company->id/employees/$employee->id/base-schedule", [
    //         'base_schedule' => TestUtils::mockBaseSchedule(8, 17)->toArray()
    //     ]);

    //     $response->assertStatus(400);
    // }

    // /** @test */
    // public function ending_time_cannot_be_after_company_ending_time()
    // {
    //     $company = Company::factory()->create(['settings' => [
    //         'base_schedule' => TestUtils::mockBaseSchedule(9, 17)->toArray()
    //     ]]);
    //     $employee = Employee::factory()->for($company)->create(['settings' => [
    //         'base_schedule' => TestUtils::mockBaseSchedule(9, 17)->toArray()
    //     ]]);
    //     $this->actingAs($employee->user, 'api');

    //     $response = $this->put("locations/$company->id/employees/$employee->id/base-schedule", [
    //         'base_schedule' => TestUtils::mockBaseSchedule(9, 18)->toArray()
    //     ]);

    //     $response->assertStatus(400);
    // }
}