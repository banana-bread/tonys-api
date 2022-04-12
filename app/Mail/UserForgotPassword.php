<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;

class UserForgotPassword extends Mailable
{

    public string $url;
    public string $email;

    public function __construct(string $email)
    {   
        $this->email = $email;
        
        $user = User::where('email', $this->email)->first();

        $baseUrl = $user->isClient()
            ? config('app.client_spa_url')
            : config('app.man_spa_url'); 

        $this->url = 
            $baseUrl.  
            '/password/reset?signed-url='.
            rawurlencode( URL::temporarySignedRoute( 'user-reset-password', now()->addDay() ) ).
            '&email='.rawurlencode($this->email);

    }

    public function build()
    {
        return $this->from('simplebarberapp@gmail.com', 'Simple Barber')           
            ->subject('Password reset requested')
            ->markdown('email.user-forgot-password');
    }
}
