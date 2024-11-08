<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'email_verified_at' => now(),
            'subscribed_to_emails' => true,
            'password' => '$2y$10$RER0OZlftD.ERquv5Xm46uaSnhxZ/VTw0ZdFDELyR6aNQbqWYKc06', // passwordSucks
            'remember_token' => Str::random(10),
        ];
    }

    public function unsubscribed()
    {
        return $this->state(function (array $attributes) {
            return [
                'subscribed_to_emails' => false,
            ];
        });
    }
}
