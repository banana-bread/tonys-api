<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public Company $company;


    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->company = $this->booking->employee->company;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = auth()->user()->id == $this->booking->client_id
            ? 'email.client-booking-cancellation-confirmation'
            : 'email.employee-booking-cancellation-confirmation';
            
        return $this->from('simplebarberapp@gmail.com')
            ->subject("Booking Cancelled" )
            ->markdown($template);
    }
}
