<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class EmployeeAuthorizationException extends BaseException
{
    protected $httpStatus = 400;
    protected $errorType = 'model-validation';
    protected $publicMessage = 'There was a model validation exception.';
}