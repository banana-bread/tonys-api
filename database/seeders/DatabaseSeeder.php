<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Service;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// TODO: this whole class is a mess.... should clean this up one day.... maybe
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
        $this->createClients();
        $allEmployeeSchedules = $this->createEmployeeSchedules($employees);
        $this->createEmployeeTimeslots($allEmployeeSchedules);
        $this->createServiceDefinitions();        
        $this->createBookings();     
        $this->createCompany();
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
            'time_slots',
            'users',
            'company',
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

    private function createClients(): void
    {
        Client::factory()->count(500)->create();
    }

    private function createEmployeeSchedules(Collection $employees): Collection
    {
        $startDate = Carbon::today()->subMonths(1); 
        $endDate = Carbon::today()->addMonths(1);   
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

    private function createEmployeeTimeSlots(Collection $allEmployeeSchedules): void
    {
        foreach($allEmployeeSchedules as $employeeSchedules)
        {
            foreach($employeeSchedules as $schedule)
            {
                if ($schedule->hoilday || $schedule->weekend) {continue;}

                $totalMinutesInDay = $schedule->end_time->diffInMinutes($schedule->start_time);
                $totalHalfHourSlots = $totalMinutesInDay / 30; // time slots are 30 minutes long

                for ($i = 0; $i < $totalHalfHourSlots; $i++)
                {
                    // 10% chance the slot gets reserved
                    $reserved = rand(1, 10) < 2 ? true : false;
                    
                    $startTime = $schedule->start_time->copy()->addMinutes($i * 30);
                    $endTime = $startTime->copy()->addMinutes(30);

                    TimeSlot::create([
                        'employee_id' => $schedule->employee_id,
                        'reserved' => $reserved,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                }
            }
        }
    }

    private function createServiceDefinitions(): void
    {
        ServiceDefinition::factory()->create(['name' => 'Child Cut']);
        ServiceDefinition::factory()->create(['name' => 'Beard Trim']);
        ServiceDefinition::factory()->create(['name' => 'Hair Cut']);
    }

    private function createBookings(): void
    {
        $clients = Client::get();
        $hairCutServiceDefinition = ServiceDefinition::where('name', 'Hair Cut')->first();

        TimeSlot::where('reserved', false)
                ->chunk(3000, function ($timeSlots) use ($clients, $hairCutServiceDefinition){
                    $timeSlots->each( function($slot) use ($clients, $hairCutServiceDefinition) {

                        // 70% chance a booking is created
                        $shouldCreateBooking = rand(1, 10) > 3 ? true : false;

                        if ($shouldCreateBooking)
                        {
                            $booking = Booking::create([
                                'client_id' => $clients->random()->id,
                                'employee_id' => $slot->employee_id,
                                'started_at' => $slot->start_time,
                                'ended_at' => $slot->end_time,
                            ]);

                            Service::create([
                                'service_definition_id' => $hairCutServiceDefinition->id,
                                'booking_id' => $booking->id
                            ]);  
                            
                            $slot->reserved = true;
                            $slot->save();
                        }
                    });
                });
    }

    private function createCompany()
    {
        Company::factory()->create();
    }
}
