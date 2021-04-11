<?php

namespace App\Services\Auth;

use App\Helpers\Days;
use App\Jobs\CreateEmployeeTimeSlots;
use App\Mail\CompanyCreated;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use App\Mail\EmployeeRegistered;
use App\Models\Company;
use Illuminate\Support\Arr;

class RegisterService
{
    public function employee(array $attributes): Employee
    {
        $user = User::create(
            Arr::except($attributes, ['admin', 'owner', 'company_id', 'settings'])
        );

        $employee = $user->employee()->create([
            'admin' => Arr::get($attributes, 'admin'),
            'owner' => Arr::get($attributes, 'owner'),
            'company_id' => Arr::get($attributes, 'company_id'),
            'settings' => Arr::get($attributes, 'settings'),
        ]);

        $employee->send(new EmployeeRegistered());

        CreateEmployeeTimeSlots::dispatch($employee, Days::YEAR);

        return $employee;
    }

    public function company(array $attributes): Company
    {
        $company = Company::create(Arr::except($attributes, 'user'));
        $user = User::create(Arr::get($attributes, 'user'));

        $employee = $user->employee()->create([
            'admin' => true,
            'owner' => true,
            'company_id' => $company->id,
            'settings' => $company->settings,
        ]);

        $employee->send(new CompanyCreated($company));

        CreateEmployeeTimeSlots::dispatch($employee, Days::YEAR);

        return $company->load('owner');
    }

    public function client(array $attributes): Client
    {
        $user = User::create($attributes);

        return $user->client()->create();
    }
}