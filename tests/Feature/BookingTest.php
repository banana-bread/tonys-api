<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Employee;
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
    public function a_booking_requires_a_client_and_an_employee(): void
    {

    }

    /** @test */
    public function a_client_can_reserve_a_booking(): void
    {

    }

    /** @test */
    public function a_client_cannot_reserve_multiple_bookings_with_overlapping_times(): void
    {

    }

    /** @test */
    public function an_employee_cannot_be_reserved_for_multiple_bookings_with_overlapping_times(): void
    {

    }

    /** @test */
    public function an_employee_can_override_their_own_bookings(): void
    {

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
}
