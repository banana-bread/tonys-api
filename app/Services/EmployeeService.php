<?php

namespace App\Services;

use App\Models\Employee;
use App\Exceptions\EmployeeAuthorizationException;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function update(array $attributes, string $id): Employee
    {
        return DB::transaction(function () use ($attributes, $id) {
            $employee = Employee::findOrFail($id);
            $employee->fill($attributes);
            $employee->save();
            
            return $employee;
        });
    }
}
