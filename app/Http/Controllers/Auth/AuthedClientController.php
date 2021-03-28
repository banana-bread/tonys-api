<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class AuthedClientController extends ApiController
{
    public function get(): JsonResponse
    {
       return $this->success(
           ['authed-client' => auth()->user()->client],
           'Authed client retreived'
       );
    }
}
