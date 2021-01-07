<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class BookingException extends BaseException
{
    protected $httpStatus = 400;
    protected $errorType = 'booking';
    protected $publicMessage = 'There was a booking exception.';
}
