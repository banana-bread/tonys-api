<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserForgotPasswordRequest;
use App\Mail\UserForgotPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class UserForgotPasswordController extends ApiController
{
    public function store(UserForgotPasswordRequest $request): JsonResponse
    {
        $userEmail = request('email');

        Mail::to($userEmail)->send(new UserForgotPassword($userEmail));

        return $this->created(null, 'Password reset link sent.');
    }
}
