<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

abstract class CreateUserRequest extends FormRequest
{
    protected function userRules()
    {
        // TODO: should probably add 'owner' and also need to look into password validation stuff
        return [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'phone'    => 'phone:CA',
            'password' => 'required|string', // TODO: fix this. more strict and required_unless rule with provider
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if (! $this->password) { return; }

        $this->merge([
            'password' => Hash::make($this->password)
        ]);
    }
}
