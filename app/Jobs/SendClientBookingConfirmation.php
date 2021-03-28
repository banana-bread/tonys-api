<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
/*
    NOTES:
    -  In order for jobs to be processed on a queue, there needs to be a worker daemon running 
       on the server to process them.  Do that by running 'php artisan queue:work'.

    - When starting the queue, use options --tries and --timeout to set limits.
    
    -  If any code changes were made while the queue is running, you need to restart the
       queue before any new jobs can be processed.  Do that by running 'php artisan queue:restart


*/

class SendClientBookingConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * 
     * NOTe: dependencies can be injected in the parameter of handle method
     *
     * @return void
     */
    public function handle()
    {
        // from the docs
        // https://laravel.com/docs/8.x/mail#sending-mail


        // Mail::to($request->user())->send(new OrderShipped($order));

        logger('sending email...');
    }
}
