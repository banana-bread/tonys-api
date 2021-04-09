<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\TestMock;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

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
            },
            'company_id' => function() {
                return Company::factory()->create()->id;
            },
            'admin' => false,
            'settings' => TestMock::employee_settings(),
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'admin' => true
            ];
        });
    }
}
