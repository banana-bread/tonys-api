<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class ScheduleException extends BaseException
{
    protected $httpStatus = 400;
    protected $errorType = 'schedule';
    protected $publicMessage = 'There was a schedule exception.';
}
