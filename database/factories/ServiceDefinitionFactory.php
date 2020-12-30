<?php

namespace Database\Factories;

use App\Models\ServiceDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceDefinitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceDefinition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'price' => collect([1500, 2000, 3000])->random(), // $15, $20, or $30
            'duration' => collect([900, 1800, 2700])->random() // 15, 30, or 45 minutes
        ];
    }
}
