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
            'owner' => false,
            'bookings_enabled' => true,
            'settings' => TestMock::employee_settings(),
            'ordinal_position' => 0,
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

    public function owner()
    {
        return $this->state(function (array $attributes) {
            return [
                'admin' => true,
                'owner' => true,
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'bookings_enabled' => false,
            ];
        });
    }

    public function no_days_off()
    {
        return $this->state(function (array $attributes) {
            return [
                'settings' => TestMock::employee_settings_no_days_off()
            ];
        });
    }

    public function no_working_days()
    {
        return $this->state(function (array $attributes) {
            return [
                'settings' => TestMock::employee_settings_no_working_days()
            ];
        });
    }

    public function days_end_on_quarter_hour()
    {
        return $this->state(function (array $attributes) {
            return [
                'settings' => TestMock::employee_settings_base_schedule_ends_on_quarter_hour()
            ];
        });
    }
}
