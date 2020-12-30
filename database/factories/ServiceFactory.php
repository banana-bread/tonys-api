<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'service_definition_id' => function() {
                return ServiceDefinition::factory()->create()->id;
            },
            'booking_id' => function() {
                return Booking::factory()->create()->id;
            }
        ];
    }
}
