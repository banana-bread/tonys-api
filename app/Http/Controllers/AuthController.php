<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function registerClient(Request $request)
    {
        $service = new AuthService();
        $client = $service->registerClient($request);

        return $client;
    }

    public function registerEmployee(Request $request)
    {
        $service = new AuthService();
        $employee = $service->registerEmployee($request);

        return $employee;
    }

    public function login(Request $request)
    {
        $service = new AuthService();
        $response = $service->login($request);

        return $response;
    } 

    public function logout()
    {
       $service = new AuthService();
       $response = $service->logout();

       return $response;
    }
}
