<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Services\Auth\LoginService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientLoginController extends ApiController
{
    public function login(Request $request): ?JsonResponse
    {   
        $service = new LoginService();
        $token = $service->loginWithPassport($request);

        if (! auth()->user()->isEmployee())
        {
            throw new AuthorizationException('Must log in with management app account.', 400);  
        } 

        return $this->ok($token, 'User logged in.');
    }
}
