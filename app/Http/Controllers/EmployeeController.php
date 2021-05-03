<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Models\Employee;
use App\Services\Auth\RegisterService;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;

class EmployeeController extends ApiController
{
    public function index(): JsonResponse
    {
        // TODO: scope this to company and write test!
        $employees = Employee::all();

        return $this->ok(['employees' => $employees], 'Employees retrieved.');
    }

    public function store(CreateEmployeeRequest $request): JsonResponse
    {
        $service = new RegisterService();
        $employee = $service->employee($request->all());
            
        return $this->created(['employee' => $employee], 'Employee created.');
    }

    public function show(string $id)
    {
        $employee = Employee::findOrFail($id);

        return $this->ok(['employee' => $employee], 'Employee account retrieved.');
    }

    public function update(EmployeeRequest $request, $id): JsonResponse
    {
        $service = new EmployeeService();
        $employee = $service->update($request->all(), $id);
    
        return $this->ok($employee, 'Employee profile updated.');
    }

    public function destroy($id)
    {
        //
    }
}
