<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

class LogoutService
{
    public function logout(): void
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
    }
}
