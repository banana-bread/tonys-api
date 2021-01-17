<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class EmployeeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function an_employee_can_create_an_account(): void
    {
        $response = $this->post('/employees', [ 
            'name' => $this->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+18195551234',
            'password' => 'password',
            'admin' => false
        ]);

        $response->assertCreated();
    }

       /** @test */
       public function an_employee_can_create_an_admin_account(): void
       {
           $response = $this->post('/employees', [
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => '+18195551234',
                'password' => 'password',
                'admin' => true
            ]);
   
           $response->assertCreated();
       }
}