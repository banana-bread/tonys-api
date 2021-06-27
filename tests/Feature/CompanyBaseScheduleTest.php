<?php

namespace Tests\Feature;

use App\Jobs\UpdateEmployeeBaseSchedule;
use App\Models\Company;
use App\Models\Employee;
use App\Models\TimeSlot;
use Facade\FlareClient\Time\Time;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestUtils;
use Illuminate\Support\Facades\Bus;

class CompanyBaseScheduleTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_owner_can_update_the_company_base_schedule()
    {
        
    }

    /** @test */
    public function an_admin_cannot_update_the_company_base_schedule()
    {
        
    }

    /** @test */
    public function an_employee_cannot_update_the_company_base_schedule()
    {
        
    }

    /** @test */
    public function an_owner_cannot_update_another_companys_base_schedule()
    {
        
    }

    /** @test */
    public function an_owner_cannot_update_company_base_schedule_if_employee_hours_exist_outside_of_new_company_hours()
    {
        
    }




}