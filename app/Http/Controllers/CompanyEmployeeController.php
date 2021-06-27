<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class CompanyEmployeeController extends ApiController
{
    public function index(string $companyId): JsonResponse
    {
        return $this->ok(
            ['employees' => Employee::forCompany($companyId)->get()], 'Employees retrieved.'
        );
    }
}
