<?php

namespace Database\Factories;

use App\Models\TimeSlot;
use App\Models\Client;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeSlotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TimeSlot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startedAt = Carbon::today()->subDays(rand(0, 90)) // within last 3 months
                                    ->addHours(9)          // shop opens at 9am 
                                    ->addHours(rand(0, 9)) // hour starts between 9am and 6pm
                                    ->addMinutes(          // minute starts on :00, or :30
                                        collect([0, 30])->random()
                                    );


        $endedAt = $startedAt->copy()->addMinutes(30);
        $employee = Employee::factory()->create();

        return [
            'employee_id' => $employee->id,
            'company_id' => $employee->company_id,
            'reserved' => false,
            'employee_working' => true,
            'start_time' => $startedAt,
            'end_time' => $endedAt
        ];
    }    

    public function reserved()
    {
        return $this->state(function (array $attributes) {
            return [
                'reserved' => true
            ];
        });
    }
}
