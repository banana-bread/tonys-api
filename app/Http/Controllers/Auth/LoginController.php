<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Services\Auth\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App;

class LoginController extends ApiController
{

    public function login(Request $request): JsonResponse
    {   
        $service = new LoginService();
        $token = $service->loginWithPassport($request);

        return $this->success($token, 'User logged in.');
    }
    // TODO: figure this stuff out
    // Think I will restrict these to clients, employees should have to sign up with regular creds
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        $providerUser = Socialite::driver($provider)->stateless()->user();

        // create new user
        $user = User::firstOrCreate(
            [
                'provider_id' => $providerUser->getId()
            ],
            [
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'provider' => $provider,
            ]
        );

        $new = new Request([
            'grant_type' => 'authorization_code',
            'client_id' => config('services.passport.client_id'),
            'client_secret'  => config('services.passport.client_secret'),
            'token' => $providerUser->token
        ]);
        \Log::info($new);
        // "grant_type": "facebook",
        // "client_id": "id",
        // "client_secret": "secret",
        // "token": "facebook_oauth_token"
        $response = App::call('\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken', [$new]);

    }
}