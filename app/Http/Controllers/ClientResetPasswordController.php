<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ClientResetPasswordController extends ApiController
{
    public function store(): JsonResponse
    {
        if (! request()->hasValidSignature())
        {
            return $this->error('Reset url is invalid', 403);
        }

        User::where('email', request('email'))
            ->update([
                'password' => Hash::make( request('password') )
            ]);
            
        return $this->ok(null, 'Password updated.');
    }
}
