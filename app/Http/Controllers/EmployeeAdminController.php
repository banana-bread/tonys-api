<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Models\Employee;
use App\Services\Auth\RegisterService;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;

class EmployeeAdminController extends ApiController
{
    public function store(string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->upgrade();

        return $this->created(['employee' => $employee], 'Employee status upgraded.');
    }

    public function destroy(string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->downgrade();

        return $this->deleted('Employee status downgraded.');
    }
}
