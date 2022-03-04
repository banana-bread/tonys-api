<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCreated extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public Company $company;
    public string $service_names;
    public string $bookingUrl;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->company = $this->booking->employee->company;
        $this->service_names = $this->booking->services->pluck('name')->join(', ');
        $this->bookingUrl = env('CLIENT_SPA_URL') . '/bookings';
    }
    
    public function build()
    {
        return $this->from('simplebarberapp@gmail.com')
            ->subject($this->booking->employee->company->name . " Booking Confirmation!" )
            ->markdown('email.client-booking-confirmation');
    }
}
