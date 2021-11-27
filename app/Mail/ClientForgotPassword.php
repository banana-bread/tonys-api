<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;

class ClientForgotPassword extends Mailable
{

    public string $url;
    public string $email;

    public function __construct(string $email)
    {   
        $this->email = $email;

        $this->url = 
            config('app.client_spa_url').  
            '/password/reset?signed-url='.
            rawurlencode( URL::temporarySignedRoute( 'client-reset-password', now()->addWeek() ) ).
            '&email='.rawurlencode($this->email);

    }

    public function build()
    {
        return $this->from('adriano@example.com')
            ->subject('Simple Barber password reset requested')
            ->markdown('email.client-forgot-password');
    }
}
