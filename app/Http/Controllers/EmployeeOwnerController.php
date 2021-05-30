<?php

namespace App\Http\Controllers;

use App\Models\EmployeeOwner;
use Illuminate\Http\JsonResponse;

class EmployeeOwnerController extends ApiController
{
    public function store(string $companyId, string $id): JsonResponse
    {
        $this->authorize('create', EmployeeOwner::class);

        $employee = EmployeeOwner::forCompany($companyId)->findOrFail($id);
        $employee->create();

        return $this->created(['employee' => $employee], 'Employee status upgraded.');
    }

    public function destroy(string $companyId, string $id): JsonResponse
    {
        $this->authorize('delete', EmployeeOwner::class);

        $employee = EmployeeOwner::forCompany($companyId)->findOrFail($id);
        $employee->delete();

        return $this->deleted('Employee status downgraded.');
    }
}