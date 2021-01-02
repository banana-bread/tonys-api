<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Service;
use App\Models\ServiceDefinition;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->init();

        $employees = $this->createEmployees();
        $clients = $this->createClients();
        $allEmployeeSchedules = $this->createEmployeeSchedules($employees);
        $serviceDefinitions = $this->createServiceDefinitions();        
        $this->createBookings($allEmployeeSchedules, $clients, $serviceDefinitions);        
    }

    private function init(): void
    {
        if (config('app.env') === 'production') 
        {
            exit('you bad boy');
        }
        
        $tables = [
            'bookings',
            'clients',
            'employees',
            'employee_schedules',
            'services',
            'service_definitions',
            'users',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table)
        {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }

    private function createEmployees(): Collection
    {
        $employees = Employee::factory()->count(6)->create();
        $managers = Employee::factory()->count(2)->admin()->create();

        return $employees->concat($managers);
    }

    private function createClients(): Collection
    {
        return Client::factory()->count(500)->create();
    }

    private function createEmployeeSchedules(Collection $employees): Collection
    {
        $startDate = Carbon::today()->subMonths(1); 
        $endDate = Carbon::today()->addMonths(3);   
        $numberOfDays = $startDate->diffInDays($endDate);

        $scheduleDates = new Collection();
    
        for ($i = 0; $i < $numberOfDays; $i++)
        {
            $scheduleDates->push($startDate->copy()->addDays($i));
        }
 
        $allEmployeeSchedules = $employees->keyBy('id')->map( function($employee) use ($scheduleDates) {

            $employeeSchedules = new Collection();

            // 1. Each employee definitley works mon, tues, wed, and thurs
            // 2. Employees may or may not work fri and sat
            // 3. Nobody works sunday
            $employeeWeekMap = collect([
                0 => true,
                1 => true,
                2 => true,
                3 => true,
                4 => (bool) rand(0, 1),
                5 => (bool) rand(0, 1),
                6 => false
            ]);

            foreach ($scheduleDates as $date)
            {
                $dayOfWeek = $date->dayOfWeek;

                $isWeekend = $dayOfWeek === 4 || $dayOfWeek === 5;

                $startTime = $isWeekend
                    ? $date->copy()->addHours(10)  // start at 10am
                    : $date->copy()->addHours(9);  // start at 9am 

                $endTime = $isWeekend
                    ? $date->copy()->addHours(16)  // end at 4pm
                    : $date->copy()->addHours(18); // end at 6pm

                $schedule = new EmployeeSchedule();

                $schedule->employee_id = $employee->id;
                $schedule->work_date = $date;
                $schedule->start_time = $startTime;    
                $schedule->end_time = $endTime;
                $schedule->weekend = !$employeeWeekMap->get($dayOfWeek);
                $schedule->holiday = false;

                $employeeSchedules->push($schedule);
            }

            $employee->schedules()->saveMany($employeeSchedules);

            return $employeeSchedules;
        });

        return $allEmployeeSchedules;
    }

    private function createServiceDefinitions(): Collection
    {
        $serviceDefinitions = new Collection();

        $serviceDefinitions->push(ServiceDefinition::factory()->state(['name' => 'Hair Cut'])->create());
        $serviceDefinitions->push(ServiceDefinition::factory()->state(['name' => 'Child Cut'])->create());
        $serviceDefinitions->push(ServiceDefinition::factory()->state(['name' => 'Beard Trim'])->create());

        return $serviceDefinitions;
    }

    private function createBookings(Collection $allEmployeeSchedules, Collection $clients, Collection $serviceDefinitions): void
    {
        $today = Carbon::today();
        $twoWeeksFromToday = $today->addWeeks(2);
        
        foreach ($allEmployeeSchedules as $schedules)
        {
            foreach ($schedules as $schedule)
            {
                if ($schedule->weekend ||
                    $schedule->holiday ||
                    $schedule->work_date->gt($twoWeeksFromToday))
                {
                    continue;
                }

                // initialize
                $bookingEndedAt = $schedule->start_time; 

                // booking end time doesn't exceed schedule end time
                while ($bookingEndedAt->lte($schedule->end_time))
                {
                    // choose 1 or 2 services for booking
                    $applicableServiceDefinitions = $serviceDefinitions->random(rand(1, 2));
                    $bookingDuration = $applicableServiceDefinitions->sum('duration');

                    $lastBookingEndedAt = $bookingEndedAt;

                    // $bookingStartedAt = $schedule->work_date->copy()
                    $bookingStartedAt = $schedule->start_time->copy()
                        ->addSeconds($lastBookingEndedAt->secondsSinceMidnight() - $schedule->start_time->secondsSinceMidnight()) // end time of last booking
                        ->addSeconds(collect([0, 1800])->random()); // buffer between bookings is 0 or 30 minutes

                    $bookingEndedAt = $bookingStartedAt->copy()->addSeconds($bookingDuration);
                    if ($bookingEndedAt->gt($schedule->end_time))
                    {
                        continue;
                    }

                    $booking = Booking::create([
                        'client_id' => $clients->random()->id,
                        'employee_id' => $schedule->employee_id,
                        'started_at' => $bookingStartedAt,
                        'ended_at' => $bookingEndedAt,
                    ]);

                    foreach ($applicableServiceDefinitions as $serviceDefinition)
                    {
                        Service::create([
                            'service_definition_id' => $serviceDefinition->id,
                            'booking_id' => $booking->id
                        ]);
                    }                   
                }
            }
        }
    }
}
