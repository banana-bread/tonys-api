<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\CreateBookingRequest;
use Faker\Factory;

class CreateBookingRequestTest extends BaseRequestTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->requestClass = CreateBookingRequest::class;
    }

    public function validationProvider(): array
    {
        /* WithFaker trait doesn't work in the dataProvider */
        $faker = Factory::create( Factory::DEFAULT_LOCALE);

        return [
            'create_booking_request_should_fail_when_client_id_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'time_slot_id' => $faker->numberBetween(1, 100),
                    'service_definition_ids' => [
                        $faker->uuid(),
                        $faker->uuid()
                    ]
                ]
            ],
            'create_booking_request_should_fail_when_time_slot_id_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'client_id' => $faker->uuid(),
                    'service_definition_ids' => [
                        $faker->uuid(),
                        $faker->uuid()
                    ]
                ]
            ],
            'create_booking_request_should_fail_when_service_definition_ids_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'time_slot_id' => $faker->numberBetween(1, 100),
                    'client_id' => $faker->uuid()
                ]
            ],
            'create_booking_request_should_fail_when_service_definition_ids_is_empty' => [
                'passed' => false,
                'data' => [
                    'time_slot_id' => $faker->numberBetween(1, 100),
                    'client_id' => $faker->uuid(),
                    'service_definition_ids' => []
                ]
            ],
            'create_booking_request_should_fail_when_service_definition_ids_are_not_uuids' => [
                'passed' => false,
                'data' => [
                    'time_slot_id' => $faker->numberBetween(1, 100),
                    'client_id' => $faker->uuid(),
                    'service_definition_ids' => [$faker->word]
                ]
            ],
            'create_booking_request_should_pass_when_all_data_is_provided' => [
                'passed' => true,
                'data' => [
                    'time_slot_id' => $faker->numberBetween(1, 100),
                    'client_id' => $faker->uuid(),
                    'service_definition_ids' => [$faker->uuid()]
                ]
            ],
        ];
    }
}
