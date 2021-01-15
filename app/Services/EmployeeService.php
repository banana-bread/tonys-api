<?php

namespace App\Services;

use App\Models\Employee;
use App\Exceptions\EmployeeAuthorizationException;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function create(array $attributes): Employee
    {   // TODO: figure out a way to create a trait that wraps all public functions in db transactions?
        return DB::transaction(function () use ($attributes) {
            $user = User::create($attributes);
            
            return $user->employee()->create([
                'admin' => Arr::get($attributes, 'admin'),
            ]);
        });
    }

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
