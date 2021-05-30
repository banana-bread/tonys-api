<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAdmin;
use Illuminate\Http\JsonResponse;

class EmployeeAdminController extends ApiController
{
    public function store(string $companyId, string $id): JsonResponse
    {
        $this->authorize('create', EmployeeAdmin::class);

        $employee = EmployeeAdmin::forCompany($companyId)->findOrFail($id);
        $employee->create();

        return $this->created(['employee' => $employee], 'Employee status upgraded.');
    }

    public function destroy(string $companyId, string $id): JsonResponse
    {
        $this->authorize('delete', EmployeeAdmin::class);

        $employee = EmployeeAdmin::forCompany($companyId)->findOrFail($id);
        $employee->delete();

        return $this->deleted('Employee status downgraded.');
    }
}
