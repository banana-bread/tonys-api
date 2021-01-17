<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use App;

class LoginService
{
    public function loginWithPassport(Request $request)
    {

        $request->request->add([
            'grant_type'     => 'password',
            'client_id'      => config('services.passport.client_id'),
            'client_secret'  => config('services.passport.client_secret'),
        ]);
            \Log::info($request);
        $response = App::call('\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken', [$request]);

        return $response->content();
    }

    public function loginWithProvider(array $attributes, string $provider)
    {
        
    }
}
