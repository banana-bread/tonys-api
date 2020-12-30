<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Passport\Exceptions\OAuthServerException;
use Throwable;

class Handler extends ExceptionHandler
{
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


    public function render($request, Throwable $t)
    {
        if ($t instanceof OAuthServerException)
            return $this->handleOAuthServerException($t);  
    }

    // TODO: refine this, make sure we're checking for everything
    protected function handleOAuthServerException(OAuthServerException $t)
    {
        if ($t->statusCode() === 400)
        {
            return response()->json('Username or password did not match.', $t->statusCode());
        }
        else if ($t->statusCode() === 401)
        {
            return response()->json('Your credentials are incorrect.  Please try again.', $t->statusCode());
        }

        return response()->json($t, 500);
    }

}
