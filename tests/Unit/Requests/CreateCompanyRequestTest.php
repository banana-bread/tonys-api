<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\CreateCompanyRequest;
use Faker\Factory;
use Tests\TestMock;

class CreateCompanyRequestTest extends BaseRequestTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->requestClass = CreateCompanyRequest::class;
    }

    public function validationProvider(): array
    {
        /* WithFaker trait doesn't work in the dataProvider */
        $faker = Factory::create( Factory::DEFAULT_LOCALE);

        return [
            'create_company_request_should_fail_when_name_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'address' => $faker->address,
                    'phone' => '+18196643923',
                    'time_slot_duration' => $faker->numberBetween(1000, 2000),
                    'booking_cancellation_period' => $faker->numberBetween(1000, 2000),
                    'settings' => TestMock::company_settings(),
                    'user' => $this->user_data($faker),
                ]
            ],
            'create_company_request_should_fail_when_address_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'name' => $faker->firstName,
                    'phone' => '+18196643923',
                    'time_slot_duration' => $faker->numberBetween(1000, 2000),
                    'booking_cancellation_period' => $faker->numberBetween(1000, 2000),
                    'settings' => TestMock::company_settings(),
                    'user' => $this->user_data($faker),
                ]
            ],
            'create_company_request_should_fail_when_phone_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'name' => $faker->firstName,
                    'address' => $faker->address,
                    'time_slot_duration' => $faker->numberBetween(1000, 2000),
                    'booking_cancellation_period' => $faker->numberBetween(1000, 2000),
                    'settings' => TestMock::company_settings(),
                    'user' => $this->user_data($faker),
                ]
            ],
            'create_company_request_should_fail_when_time_slot_duration_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'name' => $faker->firstName,
                    'address' => $faker->address,
                    'phone' => '+18196643923',
                    'booking_cancellation_period' => $faker->numberBetween(1000, 2000),
                    'settings' => TestMock::company_settings(),
                    'user' => $this->user_data($faker),
                ]
            ],
            'create_company_request_should_fail_when_booking_cancellation_period_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'name' => $faker->firstName,
                    'address' => $faker->address,
                    'phone' => '+18196643923',
                    'time_slot_duration' => $faker->numberBetween(1000, 2000),
                    'settings' => TestMock::company_settings(),
                    'user' => $this->user_data($faker),
                ]
            ],
            'create_company_request_should_fail_when_user_data_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'name' => $faker->firstName,
                    'address' => $faker->address,
                    'phone' => '+18196643923',
                    'time_slot_duration' => $faker->numberBetween(1000, 2000),
                    'settings' => TestMock::company_settings(),
                ]
            ],
            'create_company_request_should_fail_when_settings_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'name' => $faker->firstName,
                    'address' => $faker->address,
                    'phone' => '+18196643923',
                    'time_slot_duration' => $faker->numberBetween(1000, 2000),
                    'user' => [
                        'name' => $faker->name,
                        'email' => $faker->email,
                        'phone' => '+18196643923',
                        'password' => $faker->password,
                    ],
                ]
            ],
            'create_company_request_should_pass_when_all_data_is_provided' => [
                'passed' => true,
                'data' => [
                    'name' => $faker->firstName,
                    'address' => $faker->address,
                    'phone' => '+18196643923',
                    'time_slot_duration' => $faker->numberBetween(1000, 2000),
                    'booking_cancellation_period' => $faker->numberBetween(1000, 2000),
                    'user' => [
                        'name' => $faker->name,
                        'email' => $faker->email,
                        'phone' => '+18196643923',
                        'password' => $faker->password,
                    ],
                    'settings' => TestMock::company_settings(),
                ]
            ],
        ];
    }

    private function user_data($faker): array
    {
        return [
            'name' => $faker->name,
            'email' => $faker->email,
            'phone' => '+18196643923',
            'password' => $faker->password,
        ];
    }

}
