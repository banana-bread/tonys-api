<?php

namespace Tests\Feature\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;


class AuthControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_client_can_create_an_account(): void
    {
        $mock = new AuthControllerMock();
        $attributes = $mock->a_request_to_create_a_client_account();

        $this->post('/register/client', $attributes)
             ->assertCreated();
    }

    /** @test */
    public function an_employee_can_create_an_account(): void
    {
        $mock = new AuthControllerMock();
        $attributes = $mock->a_request_to_create_an_employee_account();

        $response = $this->post('/register/employee', $attributes)
                         ->assertCreated()
                         ->json();

        $this->assertArrayHasKey('admin', $response);
        $this->assertFalse($response['admin']);
    }

    /** @test */
    public function an_employee_can_create_an_admin_account(): void
    {
        $mock = new AuthControllerMock();
        $attributes = $mock->a_request_to_create_an_employee_admin_account();

        $response = $this->post('/register/employee', $attributes)
                         ->assertCreated()
                         ->json();

        $this->assertArrayHasKey('admin', $response);
        $this->assertTrue($response['admin']);
    }
}
