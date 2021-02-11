<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\TimeSlotRequest;
use Faker\Factory;

class TimeSlotAvailabilityTest extends BaseRequestTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->requestClass = TimeSlotRequest::class;
    }

    public function validationProvider(): array
    {
        /* WithFaker trait doesn't work in the dataProvider */
        $faker = Factory::create( Factory::DEFAULT_LOCALE);

        return [
            'time_slot_availability_request_should_fail_when_service_definition_ids_are_not_provided' => [
                'passed' => false,
                'data' => [
                    'employee-id' => $faker->uuid(),
                    'date-from' => $faker->numberBetween(5000, 70000),
                    'date-to' => $faker->numberBetween(5000, 70000)
                ]
            ],
            'time_slot_availability_request_should_fail_when_employee_id_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'service-definition-ids' => $faker->uuid(),
                    'date-from' => $faker->numberBetween(5000, 70000),
                    'date-to' => $faker->numberBetween(5000, 70000)
                ]
            ],
            'time_slot_availability_request_should_fail_when_date_from_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'service-definition-ids' => $faker->uuid(),
                    'employee-id' => $faker->uuid(),
                    'date-to' => $faker->numberBetween(5000, 70000)
                ]
            ],
            'time_slot_availability_request_should_fail_when_date_to_is_not_provided' => [
                'passed' => false,
                'data' => [
                    'service-definition-ids' => $faker->uuid(),
                    'employee-id' => $faker->uuid(),
                    'date-from' => $faker->numberBetween(5000, 70000)
                ]
            ],
            'time_slot_availability_request_should_pass_when_employee_id_is_present_but_empty' => [
                'passed' => true,
                'data' => [
                    'service-definition-ids' => $faker->uuid(),
                    'employee-id' => null,
                    'date-from' => $faker->numberBetween(5000, 70000),
                    'date-to' => $faker->numberBetween(5000, 70000)
                ]
            ],
            'time_slot_availability_request_should_pass_when_all_data_is_provided' => [
                'passed' => true,
                'data' => [
                    'service-definition-ids' => $faker->uuid(),
                    'employee-id' => $faker->uuid(),
                    'date-from' => $faker->numberBetween(5000, 70000),
                    'date-to' => $faker->numberBetween(5000, 70000)
                ]
            ],
        ];
    }
}
