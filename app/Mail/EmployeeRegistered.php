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
        return $this->from('simplebarberapp@gmail.com', 'Simple Barber')
            // TODO: create template
            ->markdown('email.employee-registered-confirmation');
    }
}
