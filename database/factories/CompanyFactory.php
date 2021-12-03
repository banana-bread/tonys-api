<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\TestMock;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->firstName . '\'s Barber Shop',
            'city' => $this->faker->city,
            'region' => 'ON',
            'postal_code' => $this->faker->postcode,
            'address' => '72 Springfield rd',
            'country' => $this->faker->country,
            'phone' => $this->faker->phoneNumber,
            'time_slot_duration' => 900,  // 15 minutes
            'booking_grace_period' => 86400, // 24 hours
            'timezone' => 'America/Toronto',
            'settings' => [],
        ];
    }
}
