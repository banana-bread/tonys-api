<?php

namespace Database\Factories;

use App\Models\Company;
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
            'company_id' => function() {
                return Company::factory()->create()->id;
            },
            'name' => $this->faker->word(),
            'price' => collect([1500, 2000, 3000])->random(), // $15, $20, or $30
            'duration' => collect([900, 1800, 2700])->random(), // 15, 30, or 45 minutes
        ];
    }

    public function short()
    {
        return $this->state(function (array $attributes) {
            return [
                'duration' => 900
            ];
        });
    }

    public function medium()
    {
        return $this->state(function (array $attributes) {
            return [
                'duration' => 1800
            ];
        });
    }

    public function long()
    {
        return $this->state(function (array $attributes) {
            return [
                'duration' => 2700
            ];
        });
    }


}
