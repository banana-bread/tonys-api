<?php

namespace App\Console\Commands;

use App\Models\TimeSlot;
use Illuminate\Console\Command;

class PurgeOldTimeSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slots:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge passed time slots.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TimeSlot::where('end_time', '<', now()->subDay())->delete();

        logger('Purged old time slots');

        return Command::SUCCESS;
    }
}
