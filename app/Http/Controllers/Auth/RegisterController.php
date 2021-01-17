<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\CreateEmployeeRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class RegisterController extends ApiController
{

    // public function employee(CreateEmployeeRequest $request, string $provider = null): JsonResponse
    // {   
    //     $service = new RegisterService();

    //     $employee = !$provider
    //         ? $service->employeeWithPassword($request->all())
    //         : $service->employeeWithProvider($request->all(), $provider);
        
    //     return $this->success($employee, 'Employee created.', 201);
    // }
    public function employee(CreateEmployeeRequest $request): JsonResponse
    {   
        $service = new RegisterService();
        $employee = $service->employee($request->all());
        
        return $this->success($employee, 'Employee created.', 201);
    }

    public function client(CreateClientRequest $request): JsonResponse
    {   
        $service = new RegisterService();
        $client = $service->client($request->all());
        
        return $this->success($client, 'Client created.', 201);
    }
}
