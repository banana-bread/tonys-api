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
        $this->createServiceDefinitions($company, $employees);        
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
        return Company::factory()->create([
            'name' => 'Tony\'s Barber Shop',
            'slug' => 'tonys'
        ]);
    }

    private function createEmployees(Company $company): Collection
    {
        $employees = new Collection();
        $employees->push(Employee::factory()->no_days_off()->for($company)->create());
        $employees->push(Employee::factory()->no_days_off()->for($company)->create());
        $employees->push(Employee::factory()->no_days_off()->for($company)->create());
        $employees->push(Employee::factory()->admin()->for($company)->create());

        $user = User::factory()->create(['first_name' => 'Milo', 'last_name' => 'Parker', 'email' => 'milo@example.com']);
        $employees->push(Employee::factory()->owner()->for($user)->for($company)->create());

        return $employees;
    }

    private function createClients(): void
    {
        Client::factory()->count(500)->create();
    }

    private function createEmployeeTimeSlots(Collection $employees): void
    {
        $employees->each(fn ($employee) => $employee->createSlotsForNext(365)); 
    }

    private function createServiceDefinitions(Company $company, Collection $employees): void
    {
        $s1 = ServiceDefinition::factory()->for($company)->create(['name' => 'Hair Cut', 'duration' => 1800, 'ordinal_position' => 0]);
        $s2 = ServiceDefinition::factory()->for($company)->create(['name' => 'Beard Trim', 'duration' => 900, 'ordinal_position' => 1]);
        $s3 = ServiceDefinition::factory()->for($company)->create(['name' => 'Child Cut', 'duration' => 2700, 'ordinal_position' => 2]);

        $services = collect([$s1, $s2, $s3]);
        $employeeIds = $employees->pluck('id');

        $services->each(fn($service) => 
            $service->employees()->sync($employeeIds)
        );
    }

    private function createBookings(): void
    {
        $clients = Client::get();
        $hairCutServiceDefinition = ServiceDefinition::where('name', 'Hair Cut')->first();

        TimeSlot::where('reserved', false)
                ->chunk(500, function ($timeSlots) use ($clients, $hairCutServiceDefinition){
                    $timeSlots->filter(fn ($slot) => $slot->employee_working)
                        ->nth(2)
                        ->each( function($slot) use ($clients, $hairCutServiceDefinition) {

                        // 70% chance a booking is created
                        $shouldCreateBooking = rand(1, 10) > 3 ? true : false;

                        if ($shouldCreateBooking)
                        {
                            $booking = Booking::create([
                                'client_id' => $clients->random()->id,
                                'employee_id' => $slot->employee_id,
                                'started_at' => $slot->start_time,
                                'ended_at' => $slot->start_time->copy()->addMinutes(30),
                            ]);

                            Service::create([
                                'service_definition_id' => $hairCutServiceDefinition->id,
                                'booking_id' => $booking->id,
                                'name' => $hairCutServiceDefinition->name,
                                'price' => $hairCutServiceDefinition->price,
                                'duration' => $hairCutServiceDefinition->duration,
                            ]);  

                            // TimeSlot::where('employee_id', $slot->employee_id)
                            //     ->where('start_time', $booking->started_at)
                            //     ->orWhere('start_time', $booking->started_at)
                            //     ->update(['booking_id' => $booking->id]);
                            
                            $slot->reserved = true;
                            $slot->booking_id = $booking->id;
                            $slot->save();

                            TimeSlot::where('id', $slot->id + 1)->update([
                                'reserved' => true,
                                'booking_id' => $slot->booking_id,
                            ]);
                        }
                    });
                });
    }
}
