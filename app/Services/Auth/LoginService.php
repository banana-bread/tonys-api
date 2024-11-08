<?php

namespace App\Services\Auth;

use App;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

// TODO: Rewrite this class.
class LoginService
{
    public function loginWithPassport(Request $request): array
    {
        $request->request->add([
            'grant_type'     => 'password',
            'client_id'      => config('services.passport.client_id'),
            'client_secret'  => config('services.passport.client_secret'),
        ]);

        return $this->requestToken($request);
    }

    public function loginWithProvider(Request $request, string $provider): array
    {
        $providerUser = Socialite::driver($provider)->stateless()->user();

        switch($provider)
        {
            case 'facebook':
               $firstName = $providerUser->offsetGet('first_name');
               $lastName = $providerUser->offsetGet('last_name');
               break;
         
            case 'google':
               $firstName = $providerUser->offsetGet('given_name');
               $lastName = $providerUser->offsetGet('family_name');
               break;
         
            default:
               $firstName = $providerUser->getName();
               $lastName = $providerUser->getName();
         }

        User::firstOrCreate(
            ['provider_id' => $providerUser->getId()],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $providerUser->getEmail(), 
                'provider' => $provider,
            ]
        );

        $request->request->replace([
            'grant_type'    => 'social', // custom grant type defined in App\Services\Auth\SocialUserResolver
            'client_id'     => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'provider'      => $provider,
            'access_token'  => $providerUser->token
        ]);

        return $this->requestToken($request);
    }

    public function refreshToken(Request $request)
    {
        $request->request->add([
            'grant_type'     => 'refresh_token',
            'client_id'      => config('services.passport.client_id'),
            'client_secret'  => config('services.passport.client_secret'),
            'scope'          => '',
        ]);

        return $this->requestToken($request);
    }

    protected function requestToken(Request $request): array
    {
        $tokenResponse =  App::call('\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken', [$request])->content();

        return json_decode($tokenResponse, true);
    }
}
