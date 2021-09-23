<?php

namespace Database\Seeders;

use App\Helpers\DayCollection;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Service;
use App\Models\User;
use App\Models\ServiceDefinition;
use App\Models\TimeSlot;
use Database\Factories\UserFactory;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


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

        // TODO: create option here to run this throught a loop, and accept a number via an artisan command.
        //       will be useful for testing multiple companies, although id rather do that with unit tests
        $company = $this->createCompany();
        $employees = $this->createEmployees($company);
        $this->createClients();
        $this->createEmployeeTimeslots($employees);
        $this->createServiceDefinitions($company);        
        $this->createBookings();     
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
            'services',
            'service_definitions',
            'time_slots',
            'users',
            'companies',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table)
        {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }

    private function createCompany(): Company
    {
        return Company::factory()->create();
    }

    private function createEmployees(Company $company): Collection
    {
        $employees = Employee::factory()->count(6)->no_days_off()->for($company)->create();
        $manager = Employee::factory()->count(1)->admin()->for($company)->create();

        $user = User::factory()->create(['name' => 'Milo', 'email' => 'milo@example.com']);
        $owner = Employee::factory()->count(1)->owner()->for($user)->for($company)->create();

        return $employees->concat($manager)->concat($owner);
    }

    private function createClients(): void
    {
        Client::factory()->count(500)->create();
    }

    private function createEmployeeTimeSlots(Collection $employees): void
    {
        $days = DayCollection::fromRange(today()->subMonth(), today()->addMonths(3));

        $employees->each( function ($employee) use ($days) {
            $days->each(function ($day) use ($employee) {
                $baseStart = $employee->base_schedule->start($day->englishDayOfWeek);
                $baseEnd = $employee->base_schedule->end($day->englishDayOfWeek);
                $singleSlotDuration = $employee->company->time_slot_duration;
    
                if ($baseStart && $baseEnd)
                {
                    $totalSecondsInWorkDay = $baseEnd - $baseStart;
                    $totalSlotsInWorkDay = floor($totalSecondsInWorkDay / $singleSlotDuration); 
    
                    for ($i = 0; $i < $totalSlotsInWorkDay; $i++)
                    {
                        $start = $day->copy()->addSeconds($baseStart + ($i * $singleSlotDuration));
                        $end = $start->copy()->addSeconds($singleSlotDuration);
                        
                        TimeSlot::create([
                            'employee_id' => $employee->id,
                            'company_id' => $employee->company_id,
                            'reserved' => false,
                            'start_time' => $start,
                            'end_time' => $end,
                        ]);
                    }
                }
            });
        });
    }

    private function createServiceDefinitions(Company $company): void
    {
        ServiceDefinition::factory()->for($company)->create(['name' => 'Child Cut', 'duration' => 2700]);
        ServiceDefinition::factory()->for($company)->create(['name' => 'Beard Trim', 'duration' => 900]);
        ServiceDefinition::factory()->for($company)->create(['name' => 'Hair Cut', 'duration' => 1800]);
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
                            $slot->booking_id = $booking->id;
                            $slot->save();
                        }
                    });
                });
    }
}
