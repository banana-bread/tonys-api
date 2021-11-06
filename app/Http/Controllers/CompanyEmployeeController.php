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
            ['employees' => Company::findOrFail($companyId)->employees], 'Employees retrieved.'
        );
    }

    public function update(string $companyId): JsonResponse
    {
        $attributes = collect( request()->all() );

        Company::findOrFail($companyId)->employees()->each(
            fn($employee) => $employee->update( $attributes->firstWhere('id', $employee->id) ) 
        );
        
        return $this->ok(
            ['company' => Company::where('id', $companyId)->with('employees')->get()], 'Employees updated.'
        );
    } 
}
