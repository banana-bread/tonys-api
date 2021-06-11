<?php

namespace Tests\Unit\Models\Employee;

use App\Helpers\BaseSchedule;
use App\Models\Company;
use App\Models\Employee;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestUtils;

class UpdateBaseScheduleTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function all_future_slots_will_be_deleted()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->no_days_off()->create();
        $ts1 = TimeSlot::factory()->for($employee)->for($company)->create(['start_time' => today()->addDay()->addHours(9), 'end_time' => today()->addDay()->addHours(9)->addMinutes(30)]);
        $ts2 = TimeSlot::factory()->for($employee)->for($company)->create(['start_time' => today()->addDay()->addHours(9)->addMinutes(30), 'end_time' => today()->addDay()->addHours(10)]);
        
        TestUtils::callMethod($employee, 'deleteFutureSlots');

        $this->assertEquals(0, $employee->time_slots()->count());
    }

    /** @test */
    public function currently_ongoing_slot_will_not_be_deleted()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->no_days_off()->create();
        $ts = TimeSlot::factory()->for($employee)->for($company)->create(['start_time' => now()->subMinute(), 'end_time' => now()->addMinutes(29)]);

        TestUtils::callMethod($employee, 'deleteFutureSlots');

        $this->assertEquals(1, $employee->time_slots()->count());
    }

    /** @test */
    public function past_slots_will_not_be_deleted()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->no_days_off()->create();
        $ts1 = TimeSlot::factory()->for($employee)->for($company)->create(['start_time' => today()->subDay()->addHours(9), 'end_time' => today()->subDay()->addHours(9)->addMinutes(30)]);
        $ts2 = TimeSlot::factory()->for($employee)->for($company)->create(['start_time' => today()->subDay()->addHours(9)->addMinutes(30), 'end_time' => today()->subDay()->addHours(10)]);    

        $this->assertEquals(2, $employee->time_slots()->count());
    }

    /** @test */
    public function remaining_slots_for_today_will_be_created()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->for($company)->no_days_off()->create();  
        Carbon::setTestNow(today()->addHours(10)->addMinutes(6));

        TestUtils::callMethod($employee, 'createSlotsForToday');

        $this->assertEquals(13, $employee->time_slots()->count());
    }

    /** @test */
    public function last_remaining_slot_for_today_will_not_exceed_base_schedule_end()
    {
        $employee = Employee::factory()->create();  
        Carbon::setTestNow(today()->addHours(9)->addMinutes(40));
        TestUtils::callMethod($employee, 'createSlotsForToday');

        $this->assertTrue(
            today()->addSeconds($employee->base_schedule->end()) >= $employee->latest_time_slot->end_time
        );
    }

    /** @test */
    public function first_created_slot_for_today_will_not_overlap_currently_ongoing_slot()
    {
        $employee = Employee::factory()->create();

        TestUtils::callMethod($employee, 'createSlotsForToday');

        
        $this->assertEquals(0, $employee->time_slots()->where('start_time', '<', now())->count());
    }

    private function updatedBaseSchedule(): BaseSchedule
    {
        $start = today()->addHours(8)->timestamp - today()->timestamp;
        $end = today()->addHours(16)->timestamp - today()->timestamp;

        return new BaseSchedule([
            'monday' => [
                'start' => null,
                'end' => null,
            ],
            'tuesday' => [
                'start' => $start,
                'end' => $end,
            ],
            'wednesday' => [
                'start' => $start,
                'end' => $end,
            ],
            'thursday' => [
                'start' => $start,
                'end' => $end,
            ],
            'friday' => [
                'start' => $start,
                'end' => $end,
            ],
            'saturday' => [
                'start' => $start,
                'end' => $end,
            ],
            'sunday' => [
                'start' => null,
                'end' => null,
            ],
        ]);
    }
}