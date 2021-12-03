<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Services\Auth\LoginService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeLoginController extends ApiController
{
    public function login(Request $request): ?JsonResponse
    {   
        $user = User::where('email', request('username'))->first();

        if (! $user || ! $user->isEmployee())
        {
            throw new AuthorizationException('Must log in with management app account.', 400);  
        }

        $service = new LoginService();
        $token = $service->loginWithPassport($request);

        return $this->ok($token, 'User logged in.');
    }
}
