<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class EmployeeException extends BaseException
{
    protected $httpStatus = 400;
    protected $errorType = 'employee';
    protected $publicMessage = 'There was an Employee exception.';
}
