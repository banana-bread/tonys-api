<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class InvalidParameterException extends BaseException
{
    protected $httpStatus = 400;
    protected $errorType = 'invalid-parameter';
    protected $publicMessage = 'There was an invalid parameter exception.';
}
