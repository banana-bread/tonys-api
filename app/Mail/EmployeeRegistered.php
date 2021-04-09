<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->from('adriano@example.com')
            // TODO: create template
            ->markdown('email.client-booking-confirmation');
    }
}
