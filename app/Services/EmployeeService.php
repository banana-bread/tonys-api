<?php

namespace App\Services;

use App\Models\Employee;
use App\Exceptions\EmployeeAuthorizationException;
use Illuminate\Support\Arr;

class EmployeeService
{
    public function update(array $attributes, $id): Employee
    {
        $employee = Employee::findOrFail($id);
        
        // TODO: I feel like there's a better way of making this work...
        $adminStatusHasChanged = $employee->admin !== Arr::get($attributes, 'admin');
        $authorizedIsAdmin = auth()->user()->employee->admin;
        
        if ($adminStatusHasChanged && !$authorizedIsAdmin )
        {
            throw new EmployeeAuthorizationException([], 'Non-admin employees cannot grant or revoke admin privileges.');
        } 
        
        $employee->fill($attributes);
        $employee->save();

        return $employee;
    }
}
