<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\CreateEmployeeRequest;
use Faker\Factory;
use Tests\TestMock;

class CreateEmployeeRequestTest extends BaseRequestTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->requestClass = CreateEmployeeRequest::class;
    }

    public function validationProvider(): array
    {
        /* WithFaker trait doesn't work in the dataProvider */
        $faker = Factory::create( Factory::DEFAULT_LOCALE);

        return [
            'create_employee_request_should_fail_when_company_id_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'phone' => '+16139666528',
                    'password' => $faker->password,
                    'admin' => false,
                    'settings' => TestMock::employee_settings(),
                ]
            ],
            'create_employee_request_should_fail_when_name_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'company_id' => $faker->uuid(),
                    'email' => $faker->email,
                    'phone' => '+16139666528',
                    'password' => $faker->password,
                    'admin' => false,
                    'settings' => TestMock::employee_settings(),
                ]
            ],
            'create_employee_request_should_fail_when_email_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'company_id' => $faker->uuid(),
                    'name' => $faker->name,
                    'phone' => '+16139666528',
                    'password' => $faker->password,
                    'admin' => false,
                    'settings' => TestMock::employee_settings(),
                ]
            ],
            'create_employee_request_should_fail_when_password_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'company_id' => $faker->uuid(),
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'phone' => '+16139666528',
                    'admin' => false,
                    'settings' => TestMock::employee_settings(),
                ]
            ],
            'create_employee_request_should_fail_when_admin_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'company_id' => $faker->uuid(),
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'phone' => '+16139666528',
                    'password' => $faker->password,
                    'settings' => TestMock::employee_settings(),
                ]
            ],
            'create_employee_request_should_fail_when_settings_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'company_id' => $faker->uuid(),
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'phone' => '+16139666528',
                    'admin' => false,
                    'password' => $faker->password,
                ]
            ],
            'create_employee_request_should_pass_when_phone_is_not_provided' => [
                'passed' => true,
                'data' => [
                    'company_id' => $faker->uuid(),
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'password' => $faker->password,
                    'admin' => false,
                    'settings' => TestMock::employee_settings(),
                ]
            ],
            'create_employee_request_should_pass_when_all_data_is_provided' => [
                'passed' => true,
                'data' => [
                    'company_id' => $faker->uuid(),
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'phone' => '+16139666528',
                    'password' => $faker->password,
                    'admin' => false,
                    'settings' => TestMock::employee_settings(),
                ]
            ],
        ];
    }
}
