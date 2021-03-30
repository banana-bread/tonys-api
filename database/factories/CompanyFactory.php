<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => 'Tony\'s Barber Shop',
            'address' => '180 Front st',
            'phone' => '613-555-1234',
            'time_slot_duration' => 1800,  // 30 minutes
            'booking_cancellation_period' => 86400 // 24 hours 
        ];
    }
}
