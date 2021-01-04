<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Passport\Exceptions\OAuthServerException;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Traits\ApiResponse;
use App\Exceptions\EmployeeAuthorizationException;
use Illuminate\Http\JsonResponse;


class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];


    public function render($request, Throwable $t): JsonResponse
    {
        \Log::info($t);

        if ($t instanceof OAuthServerException) 
        {
            return $this->handleOAuthServerException($t);
        }

        if ($t instanceof EmployeeAuthorizationException) 
        {
            return $this->handleEmployeeAuthorizationException($t);
        }

        if ($t instanceof EmployeeAuthorizationException) 
        {
            return $this->handleEmployeeAuthorizationException($t);
        }

        if ($t instanceof ValidationException) 
        {
            return $this->handleValidationException($t);
        }

        return $this->error('Unknown server error.');
    }

    // TODO: refine this, make sure we're checking for everything
    protected function handleOAuthServerException(OAuthServerException $t): JsonResponse
    {
        if ($t->statusCode() === 400)
        {
            return $this->error('Username or password did not match.', 400);
        }
        else if ($t->statusCode() === 401)
        {
            return $this->error('Your credentials are incorrect.  Please try again.', 401);
        }

        return $this->error('OAuth server error.');
    }

    protected function handleEmployeeAuthorizationException(EmployeeAuthorizationException $t): JsonResponse
    {
        return $this->error($t->getDebugMessage(), 400);
    }

    protected function handleValidationException(ValidationException $t): JsonResponse
    {
        return $this->error('The given data was invalid.', 400);
    }

}
