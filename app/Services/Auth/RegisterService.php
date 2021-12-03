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
use Illuminate\Support\Facades\DB;

class RegisterService
{
    public function employee(string $companyId): Employee
    {
        $user = User::create(
            request()->except(['admin', 'owner', 'settings', 'expires', 'signature'])
        );

        $employee = $user->employee()->create([
            'admin' => request('admin'),
            'owner' => request('owner'),
            'bookings_enabled' => true,
            'company_id' => $companyId,
            'settings' => request('settings'),
            'ordinal_position' =>  Company::findOrFail($company)->employees()->count(),
        ]);

        // $employee->send(new EmployeeRegistered());

        CreateEmployeeTimeSlots::dispatch($employee, Days::YEAR);

        return $employee;
    }

    public function company(): Company
    {
        $company = Company::create(request()->except('user'));
        $user = User::create(request('user'));

        $employee = $user->employee()->create([
            'admin' => true,
            'owner' => true,
            'bookings_enabled' => true,
            'company_id' => $company->id,
            'settings' => $company->settings,
        ]);

        $employee->send(new CompanyCreated($company));

        CreateEmployeeTimeSlots::dispatch($employee, Days::YEAR);

        return $company->load('owner');
    }

    public function client(): Client
    {
        return DB::transaction(function ()
        {
            $user = User::create(request()->all());
    
            return $user->client()->create();
        });
    }
}