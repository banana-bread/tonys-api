<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Services\Auth\LoginService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class ClientLoginController extends ApiController
{
    public function login(Request $request): ?JsonResponse
    {   
        $user = User::where('email', request('username'))->first();

        if (! $user || ! $user->isEmployee())
        {
            throw new AuthorizationException('Must log in with client app account.', 400);  
        }

        $service = new LoginService();
        $token = $service->loginWithPassport($request);

        return $this->ok($token, 'User logged in.');
    }

    public function redirectToProvider(string $provider)
    {
        $url = Socialite::driver($provider)
            ->stateless()
            ->redirect()
            ->getTargetUrl();
        
        return $this->ok(['auth_url' => $url]);
    }

    public function handleProviderCallback(Request $request, string $provider): ?JsonResponse
    {
        $service = new LoginService();
        $token = $service->loginWithProvider($request, $provider);

        // $this->_authorize();

        return $this->ok(['token' => $token], 'User logged in.');
    }

    private function _authorize()
    {
        if (! auth()->user()->isClient())
        {
            throw new AuthorizationException('Must log in with client app account.', 400);  
        } 
    }
}
