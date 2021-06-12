<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeInvitationSent extends Mailable
{
    use Queueable;

    public function build()
    {
        return $this->from('adriano@example.com')
            // TODO: create template
            ->markdown('email.employee-invitation');
    }
}
