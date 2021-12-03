<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefreshTokenController extends ApiController
{
    public function refresh(Request $request): JsonResponse
    {   
        return $this->ok(
            (new LoginService)->refreshToken($request), 'Token refreshed.'
        );
    }
    }
