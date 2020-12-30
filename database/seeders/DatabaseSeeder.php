<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. create 6 employees
        // 2. create 2 managers
        // 3. create 1000 clients
        // 4. create employee schedules for the past and next 3 months
        // 5. create 3 service definitions (hair cut, child cut, beard trim)
        // 6. create 80 bookings for each employee (assigning random clients)
        // 7. create 1 or 2 services for each employees bookings, ensuring times and
        //    dates do not overlap
        // \App\Models\User::factory(10)->create();
    }
}
