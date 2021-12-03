<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientForgotPasswordRequest;
use App\Mail\ClientForgotPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ClientForgotPasswordController extends ApiController
{
    public function store(ClientForgotPasswordRequest $request): JsonResponse
    {
        $clientEmail = request('email');

        Mail::to($clientEmail)->send(new ClientForgotPassword($clientEmail));

        return $this->created(null, 'Password reset link sent.');
    }
}
