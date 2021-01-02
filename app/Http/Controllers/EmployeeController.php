<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends ApiController
{
    public function index()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $service = new AuthService();
        $client = $service->registerEmployee($request);

        return $this->success($client, 'Employee registered.', 201);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id): JsonResponse
    {
        $service = new EmployeeService();
        $employee = $service->update($request->all(), $id);
    
        return $this->success($employee, 'Employee profile updated');
    }

    public function destroy($id)
    {
        //
    }
}
