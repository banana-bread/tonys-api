<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeScheduleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'employee_id' => function() {
                return Employee::factory()->create()->id;
            },
            'start_time' => today()->addHours(9),
            'end_time' => today()->addHours(17),
            'weekend' => false,
            'holiday' => false,
        ];
    }

    public function weekend()
    {
        return $this->state(function (array $attributes) {
            return [
                'weekend' => true
            ];
        });
    }

    public function holiday()
    {
        return $this->state(function (array $attributes) {
            return [
                'holiday' => true
            ];
        });
    }
}
