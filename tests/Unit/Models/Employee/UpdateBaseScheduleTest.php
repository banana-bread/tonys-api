<?php

namespace Tests\Unit\Models\Employee;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateBaseScheduleTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function all_future_slots_will_be_deleted()
    {
        
    }

    /** @test */
    public function currently_ongoing_time_slot_will_not_be_deleted()
    {

    }

    /** @test */
    public function past_slots_will_not_be_deleted()
    {

    }

    /** @test */
    public function remaining_slots_for_today_will_be_created()
    {
        
    }

    /** @test */
    public function last_remaining_slot_for_today_will_not_exceed_base_schedule_end()
    {
        
    }

    /** @test */
    public function first_created_slot_for_today_will_not_overlap_currently_ongoing_slot()
    {
        
    }
}