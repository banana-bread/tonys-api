<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Services\Auth\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends ApiController
{

    public function login(Request $request): ?JsonResponse
    {   
        $service = new LoginService();
        $token = $service->loginWithPassport($request);

        return $this->success($token, 'User logged in.');
    }

    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider): ?JsonResponse
    {
        $service = new LoginService();
        $token = $service->loginWithProvider($request, $provider);

        return $this->success($token, 'User logged in.');
    }
}
