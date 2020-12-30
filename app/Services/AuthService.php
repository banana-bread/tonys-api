<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App;
use App\Exceptions\AuthorizationException;
use App\Models\User;

class AuthService
{
    public function registerClient(Request $request)
    {
        $request->validate($this->getRegisterValidation());
        $user = $this->createUser($request);
        
        return $user->client()->create();
    }

    public function registerEmployee(Request $request)
    {
        $validation = $this->getRegisterValidation();
        array_push($validation, ['admin' => 'required|boolean']);

        $request->validate($validation);
        $user = $this->createUser($request);

        return $user->employee()->create(['admin' => $request->admin]);
    }

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

    private function getRegisterValidation()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];
    }

    private function createUser(Request $request)
    {
        return User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);
    }
}

