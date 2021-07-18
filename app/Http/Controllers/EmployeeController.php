<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Models\Employee;
use App\Services\Auth\RegisterService;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends ApiController
{
    public function store(CreateEmployeeRequest $request, string $companyId): JsonResponse
    {
        if (! request()->hasValidSignature())
        {
            return $this->error('Invalid url signature.', 400);
        }

        $service = new RegisterService();
        $employee = $service->employee($companyId);
            
        return $this->created(['employee' => $employee], 'Employee created.');
    }

    public function show(string $companyId, string $id)
    {
        return $this->ok(
            ['employee' => Employee::forCompany($companyId)->with('company')->findOrFail($id)], 'Employee account retrieved.'
        );
    }

    public function update(Request $request, string $companyId, string $id)
    {
        $employee = Employee::forCompany($companyId)->findOrFail($id);
        $this->authorize('update', $employee);

        $employee->user->update(request()->only(['name', 'phone']));

        return $this->ok($employee, 'Employee profile updated.');
    }

    // TODO: should make this a soft delete probably
    public function destroy($id)
    {
        //
    }


}
