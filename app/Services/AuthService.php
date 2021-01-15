<?php

namespace App\Services;

use Illuminate\Http\Request;
use App;

class AuthService
{
    public function login(Request $request)
    {

        $request->request->add([
            'grant_type'     => 'password',
            'client_id'      => config('services.passport.client_id'),
            'client_secret'  => config('services.passport.client_secret'),
        ]);

        $response = App::call('\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken', [$request]);

        return $response->content();
    }

    // TODO: move exception handling to handler.php
    public function logout()
    {
        try
        {
            auth()->user()->tokens->each(function ($token, $key) {
                $token->delete();
            });
    
            return response()->json('Logged out.', 200);
        }
        catch (\Throwable $e)
        {
            return response()->json('Couldn\'t log out.', $e->statusCode());
        }
    }
}

