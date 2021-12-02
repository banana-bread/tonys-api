<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;

class CreateTimeSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slots:create
                            {employee : Employee id to create slots for}
                            {--d|days= : Number of days to create slots for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create future time slots for employees.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $employee = Employee::findOrFail( $this->argument('employee') );
        $days = $this->option('days') ?: 365;
        
        $employee->createSlotsForNext($days);

        $this->info('Created slots for next ' . $days . ' days for employee ' . $this->argument('employee'));

        return 0;
    }
}
