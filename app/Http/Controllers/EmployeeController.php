<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends ApiController
{
    public function index()
    {
        //
    }

    // public function store(EmployeeRequest $request): JsonResponse
    // {
    //     $service = new EmployeeService();
    //     $client = $service->create($request->all());

    //     return $this->success($client, 'Employee created.', 201);
    // }

    public function show($id)
    {
        //
    }

    public function update(EmployeeRequest $request, $id): JsonResponse
    {
        $service = new EmployeeService();
        $employee = $service->update($request->all(), $id);
    
        return $this->success($employee, 'Employee profile updated.');
    }

    public function destroy($id)
    {
        //
    }
}
