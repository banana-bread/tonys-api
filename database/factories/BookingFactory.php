<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

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
                                    ->addMinutes(          // minute starts on :00, :15, :30, or :45
                                        collect([0, 15, 30, 45])->random()
                                    );


        $endedAt = $startedAt->copy()->addMinutes(30);

        return [
            'client_id' => function() {
                return Client::factory()->create()->id;
            },
            'employee_id' => function() {
                return Employee::factory()->create()->id;
            },
            'started_at' => $startedAt,
            'ended_at' => $endedAt
        ];
    }

    public function shorter()
    {
        return $this->state(function (array $attributes) {
            return [
                'ended_at' => $attributes['started_at']->copy()->addMinutes(15)
            ];
        });
    }

    public function longer()
    {
        return $this->state(function (array $attributes) {
            return [
                'ended_at' => $attributes['started_at']->copy()->addMinutes(45)
            ];
        });
    }
}
