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
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'time_slot_duration' => 1800,  // 30 minutes
            'booking_grace_period' => 86400, // 24 hours
            'settings' => [],
        ];
    }
}
