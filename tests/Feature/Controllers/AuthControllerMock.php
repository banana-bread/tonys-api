<?php

namespace Tests\Feature\Controllers;

use Faker\Factory;
use Faker\Generator;

class AuthControllerMock
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();    
    }

    public function a_request_to_create_a_client_account(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => 'password',
        ];
    }

    public function a_request_to_create_an_employee_account(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => 'password',
            'admin' => false
        ];
    }

    public function a_request_to_create_an_employee_admin_account(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => 'password',
            'admin' => true
        ];
    }



}