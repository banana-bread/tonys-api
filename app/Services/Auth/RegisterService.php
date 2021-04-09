<?php

namespace App\Services\Auth;

use App\Helpers\Days;
use App\Jobs\CreateEmployeeSchedules;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use App\Mail\EmployeeRegistered;
use Illuminate\Support\Arr;

class RegisterService
{
    public function employee(array $attributes): Employee
    {
        $user = User::create(
            Arr::except($attributes, ['admin', 'company_id', 'settings'])
        );

        $employee = $user->employee()->create([
            'admin' => Arr::get($attributes, 'admin'),
            'company_id' => Arr::get($attributes, 'company_id'),
            'settings' => Arr::get($attributes, 'settings'),
        ]);

        // TODO: implement this 
        $employee->send(new EmployeeRegistered());

        CreateEmployeeSchedules::dispatch($employee, Days::YEAR);

        return $employee;
    }

    public function client(array $attributes): Client
    {
        $user = User::create($attributes);

        return $user->client()->create();
    }
}