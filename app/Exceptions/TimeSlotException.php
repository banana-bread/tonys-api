<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class BookingException extends BaseException
{
    protected $httpStatus = 500;
    protected $errorType = 'time-slot';
    protected $publicMessage = 'There was a time slot exception.';
}
