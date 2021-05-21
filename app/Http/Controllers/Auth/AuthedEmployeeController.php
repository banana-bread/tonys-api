<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class AuthedEmployeeController extends ApiController
{
    public function show(): JsonResponse
    {
       return $this->ok(
           ['employee' => auth()->user()->employee], 'Authed employee retreived'
        );
    }
}
