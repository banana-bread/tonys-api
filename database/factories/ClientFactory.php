<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function() {
                return User::factory()->create()->id;
            }
        ];
    }

    public function unsubscribed()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => function () {
                    return User::factory()->unsubscribed()->create()->id;
                }
            ];
        });
    }
}
