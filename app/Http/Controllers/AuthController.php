<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $service = new AuthService();
        $token = $service->login($request);

        return $this->success($token, 'User logged in.');
    } 

    public function logout(): JsonResponse
    {
       $service = new AuthService();
       $response = $service->logout();

       return $this->success(null, 'User logged out.');
    }
}
