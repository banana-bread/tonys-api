<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class CompanyEmployeeController extends ApiController
{
    public function index(string $companyId): JsonResponse
    {
        return $this->ok(
            ['employees' => Employee::forCompany($companyId)->with('company')->get()], 'Employees retrieved.'
        );
    }

    public function update(string $companyId): JsonResponse
    {
        // TODO: would need to change if we want this route to be reusable... this is fine for now.
        Employee::where('company_id', $companyId)
            ->update(['bookings_enabled' => collect(request())->first()['bookings_enabled'] ]);
        
        return $this->ok(
            ['company' => Company::where('id', $companyId)->with('employees')->get()], 'Employees updated.'
        );
    } 
}
