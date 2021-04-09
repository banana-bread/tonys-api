<?php

namespace App\Traits;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

trait ReceivesEmails
{
    public function send(Mailable $mailable)
    {
        Mail::to($this->user)->queue($mailable);        
    }
}
