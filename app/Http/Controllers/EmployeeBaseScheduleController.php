<?php

namespace App\Http\Controllers;

use App\Helpers\BaseSchedule;
use App\Http\Requests\UpdateEmployeeBaseScheduleRequest;
use App\Jobs\UpdateEmployeeBaseSchedule;
use App\Models\Employee;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class EmployeeBaseScheduleController extends ApiController
{
    public function update(UpdateEmployeeBaseScheduleRequest $request, string $companyId, string $id)
    {
        $employee = Employee::findOrFail($id);

        if (! Gate::forUser($employee->user)->allows('update-employee-base-schedule'))
        {
            throw new AuthorizationException('User not authorized.');
        }

        UpdateEmployeeBaseSchedule::dispatch($employee, new BaseSchedule(request('base_schedule')));

        return $this->ok(['employee' => $employee], 'Employee base schedule updated');
    }
}
