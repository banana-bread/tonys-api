<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Models\Employee;
use App\Services\Auth\RegisterService;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;

class EmployeeController extends ApiController
{
    public function index(string $companyId): JsonResponse
    {
        return $this->ok(
            ['employees' => Employee::forCompany($companyId)->get()], 'Employees retrieved.'
        );
    }

    public function store(CreateEmployeeRequest $request, string $companyId): JsonResponse
    {
        $service = new RegisterService();
        $employee = $service->employee($companyId);
            
        return $this->created(['employee' => $employee], 'Employee created.');
    }

    public function show(string $companyId, string $id)
    {
        return $this->ok(
            ['employee' => Employee::forCompany($companyId)->findOrFail($id)], 'Employee account retrieved.'
        );
    }

    // TODO: need to set up model mutators in order for this to work I think.
    public function update(EmployeeRequest $request, string $companyId, string $id): JsonResponse
    {
        $employee = Employee::forCompany($companyId)->findOrFail($id);
        $employee->update(request());

        return $this->ok($employee, 'Employee profile updated.');
    }

    // TODO: should make this a soft delete probably
    public function destroy($id)
    {
        //
    }
}
