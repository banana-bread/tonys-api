<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeOwnerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_account_can_be_upgraded_to_owner()
    {

    }

    /** @test */
    public function only_owners_can_upgrade_employees_to_owner()
    {

    }

    /** @test */
    public function an_employee_account_can_be_downgraded_from_owner()
    {
        
    }

    /** @test */
    public function only_owners_can_downgrade_employees_from_owner()
    {

    }

    /** @test */
    public function an_employee_cannot_be_downgraded_from_owner_if_no_other_owners_exist()
    {
        
    }
}
