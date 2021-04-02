<?php

namespace App\Services\Auth;

use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Arr;

class RegisterService
{
    public function employee(array $attributes): Employee
    {
        /* TODO: 

            - Create employee schedules (queued job)
            - Validation on schedule creating, validate against the employees companies schedule
            - should do validation first, if it fails, dont queue the job.  else, do queue it and respond with success 

        */
        $user = User::create(
            Arr::except($attributes, ['admin', 'company_id'])
        );

        return $user->employee()->create([
            'admin' => Arr::get($attributes, 'admin'),
            'company_id' => Arr::get($attributes, 'company_id')
        ]);
    }

    public function client(array $attributes): Client
    {
        $user = User::create($attributes);

        return $user->client()->create();
    }
}