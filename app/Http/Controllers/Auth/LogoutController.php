<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Services\Auth\LogoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends ApiController
{
    public function logout(): JsonResponse
    {   
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return $this->deleted('User logged out.');
    }
}
