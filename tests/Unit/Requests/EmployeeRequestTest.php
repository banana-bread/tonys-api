<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\EmployeeRequest;
use Faker\Factory;

class EmployeeRequestTest extends BaseRequestTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->requestClass = EmployeeRequest::class;
    }

    public function validationProvider(): array
    {
        /* WithFaker trait doesn't work in the dataProvider */
        $faker = Factory::create( Factory::DEFAULT_LOCALE);

        return [
            'create_employee_request_should_fail_when_first_name_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'last_name' => $faker->lastName(),
                    'email' => $faker->email(),
                    'phone' => '+16135551234',
                    'admin' => false
                ]
            ],
            'create_employee_request_should_fail_when_last_name_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'first_name' => $faker->firstName(),
                    'email' => $faker->email(),
                    'phone' => '+16135551234',
                    'admin' => false
                ]
            ],
            'create_employee_request_should_fail_when_email_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'phone' => '+16135551234',
                    'admin' => false
                ]
            ],
            'create_employee_request_should_fail_when_phone_is_invalid_format' => [
                'passed' => false,
                'data' => [
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'email' => $faker->email(),
                    'phone' => '(123) 555-1234',
                    'admin' => false
                ]
            ],
            'create_employee_request_should_fail_when_admin_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'email' => $faker->email(),
                    'phone' => '+16135551234'
                ]
            ],
            'create_employee_request_should_pass_when_all_data_is_provided' => [
                'passed' => true,
                'data' => [
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'email' => $faker->email(),
                    'phone' => '+16135551234',
                    'admin' => false
                ]
            ],
        ];
    }
}
