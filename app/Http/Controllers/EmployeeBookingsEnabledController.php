<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class EmployeeBookingsEnabledController extends ApiController
{
    /* TODO: 
        - [ ] tests
        - [ ] request class?     
    */
    public function update(string $companyId, string $id): JsonResponse
    {
        if (! auth()->user()->isOwner() && 
              auth()->user()->employee->id !== $id)
        {
            throw new AuthorizationException('Employee not authorized to perform this action.');
        }

        $employee = Employee::findOrFail($id);
        $employee->update(['bookings_enabled' => request('bookings_enabled')]);
        
        return $this->ok($employee, 'Employee updated.');
    }
}
