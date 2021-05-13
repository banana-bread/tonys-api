<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class AuthedClientController extends ApiController
{
    public function show(): JsonResponse
    {
       return $this->ok(
           ['client' => auth()->user()->client], 'Authed client retreived'
        );
    }
}
