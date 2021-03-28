<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /*
        TODO: Figure out how to create templates with the fluent api.
    */
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('test@email.com')
            ->view('emails.bookings.created');
    }
}
